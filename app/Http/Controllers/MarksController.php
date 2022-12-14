<?php

namespace App\Http\Controllers;

use App\Http\Resources\MarksResource;
use App\Http\Resources\RegisterUserResource;
use App\Http\Resources\StudentsCollectionResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Mark;
use App\Models\Mark_modification;
use App\Models\Teacher;
use App\Models\RegisterUser;
use App\Models\Sclass;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;

class MarksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MarksResource::collection(Mark::all());
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
            'user_student_id' => 'required|exists:students,user_id',
            'subject_id' => 'required|exists:subjects,id',
            'moderator_id' => 'required|exists:register_users,id',
            'activity_id' => 'required|exists:activities,id',
            'value' => 'required|numeric|between:1,5'
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }
        $mark_datetime = Carbon::now();

        $moderator = RegisterUser::where('id', $request->moderator_id)->first();
        $isModeratorTeacher = Teacher::where('user_id', $request->moderator_id)->exists();
        $isModeratorAdmin = $moderator->isAdmin;

        if (!($isModeratorAdmin || $isModeratorTeacher)) {
            // return response('[moderator_id] The user with given id is not allowed to add a mark', 401);
            return response()->json(['status' => 401, 'data' => 'Użytkownikowi nie wolno dodawać ocen'], 401);
        }

        $newMark = Mark::create([
            'user_student_id' => $request->user_student_id,
            'subject_id' => $request->subject_id,
            'moderator_id' => $request->moderator_id,
            'activity_id' => $request->activity_id,
            'mark_datetime' => $mark_datetime,
            'value' => $request->value
        ]);

        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $request->moderator_id,
            'mark_id' => $newMark->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $newMark->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        return response($newMark, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Mark $mark)
    {
        return new MarksResource($mark);
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
        $validator = Validator::make($request->all(), [
            'moderator_id' => 'required|exists:register_users,id',
            'new_value' => 'required|numeric|between:0,5',
            'modification_reason' => 'required|string|max:199'
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }
        if (!Mark::where('id', $id)->exists()) {
            // return response("Mark with given id doesn't exist", 400);
            return response()->json(['status' => 404, 'data' => 'Ocena o podanym ID nie istnieje'], 404);
        }

        $markToUpdate = Mark::where('id', $id)->first();
        $oldMarkValue = $markToUpdate->value;

        $markToUpdate->value = $request->new_value;
        $markToUpdate->moderator_id = $request->moderator_id;
        $markToUpdate->updated_at = Carbon::now();
        $markToUpdate->save();

        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $request->moderator_id,
            'mark_id' => $id,
            'mark_before_modification' => $oldMarkValue,
            'mark_after_modification' => $request->new_value,
            'modification_reason' => $request->modification_reason
        ]);
        return response()->json(['status' => 200, 'data' => 'Edycja oceny przebiegła pomyślnie'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  int  $moderator_id
     * @return \Illuminate\Http\Response
     */
    public function deleteMarkAndCreateMarkModification($id, $moderator_id)
    {
        if (!Mark::where('id', $id)->exists()) {
            // return response("Mark with given id doesn't exist", 400);
            return response()->json(['status' => 404, 'data' => 'Ocena o podanym ID nie istnieje'], 404);
        }

        $markToDelete = Mark::find($id);
        $markValue = $markToDelete->value;
        $markToDelete->value = 0;
        $markToDelete->save();
        $modif = Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $moderator_id,
            'mark_id' => $id,
            'mark_before_modification' => $markValue,
            'mark_after_modification' => 0,
            'modification_reason' => "usunięcie oceny"
        ]);
        return response()->json(['status' => 200, 'data' => $modif], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function getStudentMarks($studentId)
    {
        if (!Student::where('user_id', $studentId)->exists()) {
            return response()->json(['status' => 404, 'data' => 'Student o podanym ID nie istnieje'], 404);
        }
        $marks = Mark::where('user_student_id', $studentId)->where('value', '<>', 0)->get();
        return MarksResource::collection($marks);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $studentId
     * @param  int  $subjectId
     * @return \Illuminate\Http\Response
     */
    public function getStudentMarksOfParticularSubject($studentUserId, $subject_id)
    {
        if (!Student::where('user_id', $studentUserId)->exists()) {
            // return response("Student with given id doesn't exist", 400);
            return response()->json(['status' => 404, 'data' => 'Student o podanym ID nie istnieje'], 404);
        }
        $marks = Mark::where('user_student_id', $studentUserId)
            ->where('subject_id', $subject_id)->where('value', '<>', 0)->get();
        return MarksResource::collection($marks);
    }
    public function getClassMarksOfParticularSubjectDividedByStudents($classId, $subjectId)
    {
        $marks = new Collection();
        $school_class = Sclass::where('id', $classId)->first();
        if ($school_class == null) {
            return response()->json(['status' => 404, 'data' => 'Klasa o podanym ID nie istnieje'], 404);
        }
        $students = Student::where('sclass_id', $school_class->id)->get();
        foreach ($students as $s) {
            $user = RegisterUser::where('id', $s->user_id)->first();
            $studentMarks = Mark::where('user_student_id', $s->user_id)
                ->where('subject_id', $subjectId)->where('value', '<>', 0)->get();
            $studentMarks = MarksResource::collection($studentMarks);
            $studentAndMarks = array(
                "student" => new RegisterUserResource($user),
                "marks" => $studentMarks
            );
            $marks->add($studentAndMarks);
        }
        return response()->json(['status' => 200, 'data' => $marks], 200);
    }
}
