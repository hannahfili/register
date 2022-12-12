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
use App\Http\Resources\SubjectsResource;
use App\Models\Sclass;

use function PHPUnit\Framework\isEmpty;


class RegisterUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $access = new Access();
        // $access->set_adminAccess();
        // $access->set_teacherAccess();
        // // $access->set_studentAccess();


        // $isEligible = Helper::userIsEligibleForResource($request->bearerToken(), $access);
        // if ($isEligible == TokenAuthResult::TokenNotFound) {
        //     return response('Token not found', 400);
        // }
        // if (!$isEligible) {
        //     return response('User is not allowed for the resource', 401);
        // }
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

        // if ($request->isTeacher == true) {
        //     $validator = Validator::make($request->all(), [
        //         'subject_id' => 'required'
        //     ]);
        //     if ($validator->fails()) {
        //         return response($validator->errors(), 400);
        //     }
        // }
        // if ($request->isStudent == true) {
        // $validator = Validator::make($request->all(), [
        //     'class_id' => 'required'
        // ]);
        // if ($validator->fails()) {
        //     return response($validator->errors(), 400);
        // }
        // }
        $token = Str::random(60);
        $newUser = RegisterUser::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'isAdmin' => $request->isAdmin,
            // 'api_token' => $token,
        ]);

        if ($request->isTeacher == true) {
            $newTeacher = Teacher::create([
                'user_id' => $newUser->id,
                // 'subject_id' => $request->subject_id
            ]);
        }
        if ($request->isStudent == true) {
            $newStudent = Student::create([
                'user_id' => $newUser->id,
                // 'class_id' => $request->class_id
            ]);
        }
        $abilities_list = Helper::createAbilitiesList($newUser);



        //CREATE TOKEN PRZY LOGIN!!!!
        $token = $newUser->createToken($newUser->email, $abilities_list)->plainTextToken;

        // return response(new RegisterUserResource($newUser), 200);
        // return response(new RegisterUserResource($newUser), 200);
        return response()->json(['status' => 200, 'data' => $token], 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RegisterUser
     * @return \Illuminate\Http\Response
     */
    public function show(RegisterUser $user)
    {
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
        // $userToUpdate = null;
        if (count($request->all()) == 0) {
            // return response('Nothing data given to update', 400);
            return response()->json(['status' => 400, 'data' => 'Nie wysłano żadnych danych'], 400);
        }
        if (RegisterUser::where('id', $id)->exists()) {
            $userToUpdate = RegisterUser::find($id);
        } else {
            // return response("User with given id doesn't exist", 400);
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
                    // return response('New email has to be different than already existing', 400);
                }
                // else {
                //     return response()->json(['status' => 400, 'data' => 'Email already exists'], 400);
                //     // return response('Email already exists', 400);
                // }
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
                // return response('Teacher id not found', 400);
                return response()->json(['status' => 404, 'data' => 'Nauczyciel o podanym ID nie istnieje'], 404);
            }
        }
        if ($request->isStudent == true && $request->has('class_id')) {
            $studentToUpdate = Student::where('user_id', $id)->first();
            if ($studentToUpdate !== null) {
                $studentToUpdate->class_id = $request->class_id;
                $studentToUpdate->save();
            } else {
                // return response('Student id not found', 400);
                return response()->json(['status' => 404, 'data' => 'Student o podanym ID nie istnieje'], 404);
            }
        }
        // echo $userToUpdate;


        return response(new RegisterUserResource($userToUpdate), 201);
        // return response($userToUpdate, 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (RegisterUser::where('id', $id)->exists()) {
            $userToDelete = RegisterUser::find($id);
            $userToDelete->delete();
            // return response('RegisterUser deleted', 200);
            return response()->json(['status' => 200, 'data' => 'Użytkownik został pomyślnie usunięty'], 200);
        }
        // return response('User with given id is not found', 400);
        return response()->json(['status' => 404, 'data' => 'Użytkownik o podanym ID nie istnieje'], 404);
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
            // return response("Student with given id doesn't exist", 400);
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
        // $teacherToUpdate = Teacher::where('user_id', $id)->first();
        // RegisterUserResource::collection(RegisterUser::all());
        return StudentsCollectionResource::collection($students);
    }
    public function getAllStudents()
    {
        return StudentsCollectionResource::collection(Student::all());
    }
    public function getTeachersNotAssignedToAnySubject()
    {
        $teachers = Teacher::where('subject_id', null)->get();
        // $teacherToUpdate = Teacher::where('user_id', $id)->first();
        // RegisterUserResource::collection(RegisterUser::all());
        return StudentsCollectionResource::collection($teachers);
    }
    public function getTeacherAssignedToThisSubject($subject_id)
    {
        $teacher = Teacher::where('subject_id', $subject_id)->first();
        return new StudentsCollectionResource($teacher);
    }
    public function getSubjectAssignedToThisTeacher($teacher_id)
    {
        if (Teacher::where('id', $teacher_id)->exists()) {

            $teacher = Teacher::where('id', $teacher_id)->first();
            if ($teacher->subject_id == null) {
                return response()->json(['status' => 404, 'data' => 'Nauczyciel nie posiada przypisanego przedmiotu'], 404);
            }
            $subject = Subject::where('id', $teacher->subject_id)->first();
            return response()->json(['status' => 200, 'data' => new SubjectsResource($subject)], 200);
        }
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
}
