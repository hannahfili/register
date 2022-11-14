<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubjectsResource;
use App\Models\Sclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Subject;
use App\Models\SclassesSubjects;
use App\Models\Teacher;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SubjectsResource::collection(Subject::all());
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
            'name' => 'required|string|unique:subjects|max:199',
            'description' => 'string|max:199'
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }
        $newSubject = Subject::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        return response($newSubject, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $subjectId
     * @param  int  $classId
     * @return \Illuminate\Http\Response
     */
    public function assignClass($subjectId, $classId)
    {
        if (!Subject::where('id', $subjectId)->exists()) return response("Subject with given id doesn't exist", 400);
        if (!Sclass::where('id', $classId)->exists()) return response("School class with given id doesn't exist", 400);
        $subject = Subject::where('id', $subjectId)->first();
        $class = Sclass::where('id', $classId)->first();

        if ($subject->sclasses()->wherePivot('sclass_id', '=', $class->id)->exists()) {
            return response('The class has already been assigned to the subject', 400);
        }


        $subject->sclasses()->attach($class);
        return response('Class assigned properly', 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $subjectId
     * @param  int  $teacherId
     * @return \Illuminate\Http\Response
     */
    public function assignTeacher($subjectId, $teacherId)
    {
        if (!Subject::where('id', $subjectId)->exists()) return response("Subject with given id doesn't exist", 400);
        if (!Teacher::where('id', $teacherId)->exists()) return response("Teacher with given id doesn't exist", 400);
        $subject = Subject::where('id', $subjectId)->first();
        $teacher = Teacher::where('id', $teacherId)->first();

        $teacher->subject_id = $subject->id;
        $teacher->save();

        return response('Teacher assigned properly', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        return new SubjectsResource($subject);
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
            'name' => 'string|unique:subjects|max:199',
            'description' => 'string|max:199'
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }
        if (count($request->all()) == 0) {
            return response('Nothing data given to update', 400);
        }
        if (Subject::where('id', $id)->exists()) {
            $subjectToUpdate = Subject::find($id);
        } else {
            return response("Subject with given id doesn't exist", 400);
        }
        if ($request->has('name')) {
            $subjectToUpdate->name = $request->name;
        }
        if ($request->has('description')) {
            $subjectToUpdate->description = $request->description;
        }
        $subjectToUpdate->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Subject::where('id', $id)->exists()) {
            $subjectToDelete = Subject::find($id);
            $subjectToDelete->delete();
            return response('Subject deleted', 200);
        }
        return response("Subject with given id doesn't exist", 400);
    }
}