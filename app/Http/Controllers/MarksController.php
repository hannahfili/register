<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Mark;

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
        //
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
            'user_teacher_id' => 'required|exists:teachers,user_id',
            'activity_id' => 'required|exists:activities,id',
            'value' => 'required|numeric|between:1,5'
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }
        $mark_datetime = Carbon::now();

        $newMark = Mark::create([
            'user_student_id' => $request->user_student_id,
            'subject_id' => $request->subject_id,
            'user_teacher_id' => $request->user_teacher_id,
            'activity_id' => $request->activity_id,
            'mark_datetime' => $mark_datetime,
            'value' => $request->value
        ]);
        return response($newMark, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
