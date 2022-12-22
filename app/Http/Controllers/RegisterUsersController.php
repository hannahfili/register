<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\RegisterUser;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Helpers\Helper;
use App\Helpers\Access;
use App\Http\Resources\RegisterUserResource;
use App\Http\Resources\RegisterUserCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Helpers\TokenAuthResult;
use App\Http\Resources\SclassesResource;
use App\Http\Resources\StudentsCollectionResource;
use App\Http\Resources\StudentsResource;
use App\Http\Resources\SubjectsResource;
use App\Models\Sclass;
use Laravel\Sanctum\PersonalAccessToken;

use function PHPUnit\Framework\isEmpty;


class RegisterUsersController extends Controller
{
    public function logOut(Request $request)
    {
        $token = $request->bearerToken();
        $tokenHashed = hash('sha256', $token);
        if (!PersonalAccessToken::where('token', $tokenHashed)->exists()) {
            return response()->json(['status' => 404, 'data' => 'W systemie nie ma wysłanego tokenu'], 404);
        }
        PersonalAccessToken::where('token', $tokenHashed)->delete();
        return response()->json(['status' => 200, 'data' => 'Wylogowano'], 200);
    }
    public function logIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }
        if (!RegisterUser::where('email', $request->email)->exists()) {
            return response()->json(['status' => 404, 'data' => "Użytkownik o podanym adresie email nie istnieje"], 404);
        }
        $hashed_password = hash('sha256', $request->password);
        $userFromDB = RegisterUser::where('email', $request->email)->first();
        $abilities_list = Helper::createAbilitiesList($userFromDB);
        if ($userFromDB->password == $hashed_password) {
            $token = $userFromDB->createToken($userFromDB->email, $abilities_list)->plainTextToken;
            return response()->json(['status' => 200, 'data' => $token], 200);
        } else {
            return response()->json(['status' => 400, 'data' => 'Błędne hasło'], 400);
        }
    }

    public function getUserAssignedToToken(Request $request)
    {
        $token = $request->token;
        $tokenHashed = hash('sha256', $token);
        $tokenFromDB = PersonalAccessToken::where('token', $tokenHashed)->first();
        if ($tokenFromDB == null) {
            return response()->json(['status' => 400, 'data' => 'Do wysłanego tokenu nie jest przypisany żaden użytkownik'], 400);
        }
        $userAssignedToToken = RegisterUser::where('id', $tokenFromDB->tokenable_id)->first();
        return new RegisterUserResource($userAssignedToToken);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return RegisterUserResource::collection(RegisterUser::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:199',
            'surname' => 'required|string|max:199',
            'email' => 'required|email|unique:register_users,email',
            'password' => 'required',
            'isAdmin' => 'required'
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }
        $password_hashed = hash('sha256', $request->password);
        $newUser = RegisterUser::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => $password_hashed,
            'isAdmin' => $request->isAdmin
        ]);

        if ($request->isTeacher == true) {
            $newTeacher = Teacher::create([
                'user_id' => $newUser->id,
            ]);
        }
        if ($request->isStudent == true) {
            $newStudent = Student::create([
                'user_id' => $newUser->id,
            ]);
        }
        return response()->json(['status' => 200, 'data' => 'Użytkownik utworzony pomyślnie'], 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUserById($id)
    {
        $user = RegisterUser::where('id', $id)->first();
        if ($user == null) {
            return response()->json(['status' => 404, 'data' => 'Użytkownik o podanym ID nie istnieje'], 404);
        }
        return new RegisterUserResource($user);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (count($request->all()) == 0) {
            return response()->json(['status' => 400, 'data' => 'Nie wysłano żadnych danych'], 400);
        }
        if (RegisterUser::where('id', $id)->exists()) {
            $userToUpdate = RegisterUser::find($id);
        } else {
            return response()->json(['status' => 404, 'data' => 'Użytkownik o podanym ID nie istnieje'], 404);
        }

        if ($request->has('name')) {
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:199'
            ]);
            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            $userToUpdate->name = $request->name;
        }
        if ($request->has('surname')) {
            $validator = Validator::make($request->all(), [
                'surname' => 'string|max:199'
            ]);
            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            $userToUpdate->surname = $request->surname;
        }
        if ($request->has('email')) {
            $validator = Validator::make($request->all(), [
                'email' => 'email'
            ]);
            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            $userWithEmail = RegisterUser::where('email', $request->email)->first();
            if ($userWithEmail === null) {
                $userToUpdate->email = $request->email;
            } else {
                if ($userWithEmail->id != $id) {
                    return response()->json(['status' => 400, 'data' => 'Email już istnieje'], 400);
                }
            }
        }
        if ($request->has('password')) {
            $userToUpdate->password = Hash::make($request->password);
        }
        if ($request->has('isAdmin')) {
            $userToUpdate->isAdmin = $request->isAdmin;
        }
        $userToUpdate->save();

        if ($request->isTeacher == true && $request->has('subject_id')) {
            $teacherToUpdate = Teacher::where('user_id', $id)->first();
            if ($teacherToUpdate !== null) {
                $teacherToUpdate->subject_id = $request->subject_id;
                $teacherToUpdate->save();
            } else {
                return response()->json(['status' => 404, 'data' => 'Nauczyciel o podanym ID nie istnieje'], 404);
            }
        }
        if ($request->isStudent == true && $request->has('class_id')) {
            $studentToUpdate = Student::where('user_id', $id)->first();
            if ($studentToUpdate !== null) {
                $studentToUpdate->class_id = $request->class_id;
                $studentToUpdate->save();
            } else {
                return response()->json(['status' => 404, 'data' => 'Student o podanym ID nie istnieje'], 404);
            }
        }


        return response(new RegisterUserResource($userToUpdate), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (PersonalAccessToken::where('tokenable_id', $id)->exists()) {
            $tokenToDelete = PersonalAccessToken::where('tokenable_id', $id)->first();
            $tokenToDelete->delete();
        }
        if (RegisterUser::where('id', $id)->exists()) {
            $userToDelete = RegisterUser::find($id);
            $userToDelete->delete();
            return response()->json(['status' => 200, 'data' => 'Użytkownik został pomyślnie usunięty'], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function dischargeStudentFromClass($studentId)
    {
        if (Student::where('id', $studentId)->exists()) {
            $student = Student::find($studentId);
        } else {
            return response()->json(['status' => 404, 'data' => 'Student o podanym ID nie istnieje'], 404);
        }
        $student->sclass_id = null;
        $student->save();

        $message = 'Uczeń %s %s został wypisany z wybranej klasy';
        $user = RegisterUser::where('id', $student->user_id)->first();
        return response()->json(['status' => 200, 'data' => sprintf($message, $user->name, $user->surname)], 200);
    }

    public function getStudentsNotAssignedToAnyClass()
    {
        $students = Student::where('sclass_id', null)->get();
        return StudentsCollectionResource::collection($students);
    }
    public function getAllStudents()
    {
        return StudentsCollectionResource::collection(Student::all());
    }
    public function getTeachersNotAssignedToAnySubject()
    {
        $teachers = Teacher::where('subject_id', null)->get();
        return StudentsCollectionResource::collection($teachers);
    }
    public function getTeacherAssignedToThisSubject($subject_id)
    {
        $teacher = Teacher::where('subject_id', $subject_id)->first();
        if ($teacher == null) {
            return response()->json(['status' => 404, 'data' => 'Do tego przedmiotu nie jest przypisany żaden nauczyciel'], 404);
        }
        return new StudentsCollectionResource($teacher);
    }
    public function getSubjectAssignedToThisTeacher($teacher_user_id)
    {
        if (!Teacher::where('user_id', $teacher_user_id)->exists()) {
            return response()->json(['status' => 404, 'data' => 'Użytkownik o podanym ID nie jest nauczycielem'], 404);
        }
        $teacher = Teacher::where('user_id', $teacher_user_id)->first();
        if ($teacher->subject_id == null) {
            return response()->json(['status' => 404, 'data' => 'Nauczyciel nie posiada przypisanego przedmiotu'], 404);
        }
        $subject = Subject::where('id', $teacher->subject_id)->first();
        return response()->json(['status' => 200, 'data' => new SubjectsResource($subject)], 200);
    }
    public function getSubjectsAssignedToThisStudent($student_id)
    {
        if (!(Student::where('id', $student_id)->exists())) {
            return response()->json(['status' => 404, 'data' => 'Uczeń o podanym ID nie istnieje'], 404);
        }
        $student = Student::where('id', $student_id)->first();
        $student_class_id = $student->sclass_id;
        if ($student_class_id == null) {
            return response()->json(['status' => 404, 'data' => 'Uczeń o podanym ID nie jest przypisany do żadnej klasy'], 404);
        }
        $school_class = Sclass::where('id', $student_class_id)->first();
        $school_class_subjects = $school_class->subjects()->get();
        return response()->json(['status' => 200, 'data' => SubjectsResource::collection($school_class_subjects)], 200);
    }
    public function getUserStudentId($user_id)
    {
        if (!(Student::where('user_id', $user_id)->exists())) {
            return response()->json(['status' => 404, 'data' => 'Użytkownik o podanym ID nie jest uczniem'], 404);
        }
        $student = Student::where('user_id', $user_id)->first();
        return response()->json(['status' => 200, 'data' => $student->id], 200);
    }
    public function getUserTeacherId($user_id)
    {
        if (!(Teacher::where('user_id', $user_id)->exists())) {
            return response()->json(['status' => 404, 'data' => 'Użytkownik o podanym ID nie jest nauczycielem'], 404);
        }
        $teacher = Teacher::where('user_id', $user_id)->first();
        return response()->json(['status' => 200, 'data' => $teacher->id], 200);
    }
}
