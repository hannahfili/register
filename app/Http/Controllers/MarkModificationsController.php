<?php

namespace App\Http\Controllers;

use App\Http\Resources\MarksResource;
use App\Http\Resources\MarkModificationsResource;
use App\Models\Mark_modification;
use App\Models\Student;
use App\Models\Mark;
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
}
