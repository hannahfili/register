<?php

namespace App\Http\Controllers;

use App\Http\Resources\MarksResource;
use App\Http\Resources\MarkModificationsResource;
use App\Http\Resources\RegisterUserResource;
use App\Models\Mark_modification;
use App\Models\Student;
use App\Models\Mark;
use App\Models\RegisterUser;
use App\Models\Sclass;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarkModificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MarkModificationsResource::collection(Mark_modification::all());
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Mark_modification::where('id', $id)->exists()) {
            return response()->json(['status' => 404, 'data' => 'Modyfikacja o podanym ID nie istnieje'], 404);
        }
        $modification = Mark_modification::where('id', $id)->first();
        return new MarkModificationsResource($modification);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function getMarksModificationsOfParticularUserStudent($studentId)
    {
        if (!Student::where('user_id', $studentId)->exists()) {
            return response("Student with given id doesn't exist", 400);
        }
        $marks = Mark::where('user_student_id', $studentId)->get();
        if ($marks->isEmpty()) {
            return response()->json(['status' => 404, 'data' => 'Wybrany uczeń nie posiada jeszcze ocen'], 404);
        }
        $modifications = new Collection();

        foreach ($marks as $mark) {
            $mark_mods = Mark_modification::where('mark_id', $mark->id)->get();
            foreach ($mark_mods as $mod) {
                $modifications->add($mod);
            }
        }

        return MarkModificationsResource::collection($modifications);
    }
    public function getClassMarksModificationsOfParticularSubjectDividedByStudents($classId, $subjectId)
    {
        $marks_modif = new Collection();
        $school_class = Sclass::where('id', $classId)->first();
        if ($school_class == null) {
            return response()->json(['status' => 404, 'data' => 'Klasa o podanym ID nie istnieje'], 404);
        }
        $students = Student::where('sclass_id', $school_class->id)->get();
        foreach ($students as $s) {
            $user = RegisterUser::where('id', $s->user_id)->first();
            $studentMarks = Mark::where('user_student_id', $s->user_id)
                ->where('subject_id', $subjectId)->get();
            $modifications = new Collection();

            foreach ($studentMarks as $mark) {
                $mark_mods = Mark_modification::where('mark_id', $mark->id)->get();
                $markAndMarkModifs = array(
                    "mark" => new MarksResource($mark),
                    "marks_modifications" => MarkModificationsResource::collection($mark_mods)
                );
                $modifications->add($markAndMarkModifs);
            }
            $studentAndMarksModifs = array(
                "student" => new RegisterUserResource($user),
                "marks_modifications" => $modifications
            );
            $marks_modif->add($studentAndMarksModifs);
        }
        return response()->json(['status' => 200, 'data' => $marks_modif], 200);
    }
    public function getStudentMarksModificationsOfParticularSubject($studentUserId, $subject_id)
    {
        $modifications = new Collection();
        if (!Student::where('user_id', $studentUserId)->exists()) {
            return response()->json(['status' => 404, 'data' => 'Student o podanym ID nie istnieje'], 404);
        }
        $marks = Mark::where('user_student_id', $studentUserId)
            ->where('subject_id', $subject_id)->get();
        foreach ($marks as $mark) {
            $mark_mods = $mark->mark_modifications()->get();
            $markAndMarkModifs = array(
                "mark" => new MarksResource($mark),
                "marks_modifications" => MarkModificationsResource::collection($mark_mods)
            );
            $modifications->add($markAndMarkModifs);
        }
        return response()->json(['status' => 200, 'data' => $modifications], 200);
    }
}
