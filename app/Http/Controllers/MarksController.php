<?php

namespace App\Http\Controllers;

use App\Http\Resources\MarksResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Mark;
use App\Models\Mark_modification;
use App\Models\Teacher;
use App\Models\RegisterUser;
use App\Models\Student;
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
            return response('[moderator_id] The user with given id is not allowed to add a mark', 401);
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
            return response("Mark with given id doesn't exist", 400);
        }

        $markToUpdate = Mark::where('id', $id)->first();
        $oldMarkValue = $markToUpdate->value;

        $markToUpdate->value = $request->new_value;
        $markToUpdate->save();

        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $request->moderator_id,
            'mark_id' => $id,
            'mark_before_modification' => $oldMarkValue,
            'mark_after_modification' => $request->new_value,
            'modification_reason' => $request->modification_reason
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  int  $moderator_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $moderator_id)
    {
        if (!Mark::where('id', $id)->exists()) {
            return response("Mark with given id doesn't exist", 400);
        }

        $markToDelete = Mark::find($id);
        echo $markToDelete;
        $markValue = $markToDelete->value;
        $markToDelete->value = 0;
        $markToDelete->save();
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $moderator_id,
            'mark_id' => $id,
            'mark_before_modification' => $markValue,
            'mark_after_modification' => 0,
            'modification_reason' => "usunięcie oceny"
        ]);
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
            return response("Student with given ID doesn't exist", 400);
        }
        $marks = Mark::where('user_student_id', $studentId)->get();
        return MarksResource::collection($marks);
        // return response($marks, 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $studentId
     * @param  int  $subjectId
     * @return \Illuminate\Http\Response
     */
    public function getStudentMarksOfParticularSubject($studentId, $subject_id)
    {
        if (!Student::where('user_id', $studentId)->exists()) {
            return response("Student with given id doesn't exist", 400);
        }
        $marks = Mark::where('user_student_id', $studentId)
            ->where('subject_id', $subject_id)->get();
        return MarksResource::collection($marks);
    }
}
