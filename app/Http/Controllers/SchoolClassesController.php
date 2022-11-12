<?php

namespace App\Http\Controllers;

use App\Http\Resources\SchoolClassesResource;
use App\Models\SchoolClass;
use App\Models\RegisterUser;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchoolClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SchoolClassesResource::collection(SchoolClass::all());
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
            'class_start' => 'required|date',
            'class_end' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }
        $newClass = SchoolClass::create([
            'name' => $request->name,
            'class_start' => $request->class_start,
            'class_end' => $request->class_end
        ]);
        return response(new SchoolClassesResource($newClass), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolClass $schoolClass)
    {
        return new SchoolClassesResource($schoolClass);
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
            return response('Nothing data given to update', 400);
        }
        if (SchoolClass::where('id', $id)->exists()) {
            $classToUpdate = SchoolClass::find($id);
        } else {
            return response("SchoolClass with given id doesn't exist", 400);
        }

        if ($request->has('name')) {
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:199'
            ]);
            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            $classToUpdate->name = $request->name;
        }
        if ($request->has('class_start')) {
            $validator = Validator::make($request->all(), [
                'class_start' => 'date'
            ]);
            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            $classToUpdate->class_start = $request->class_start;
        }
        if ($request->has('class_end')) {
            $validator = Validator::make($request->all(), [
                'class_end' => 'date'
            ]);
            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            $classToUpdate->class_end = $request->class_end;
        }
        $classToUpdate->save();
        return response(new SchoolClassesResource($classToUpdate), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (SchoolClass::where('id', $id)->exists()) {
            $classToDelete = SchoolClass::find($id);
            $classToDelete->delete();
            return response('SchoolClass deleted', 200);
        }
        return response("SchoolClass with given id doesn't exist", 400);
    }
    /**
     * Update the specified resource in storage.
     * @param  int  $classId
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function assignStudent($classId, $studentId)
    {
        if (Student::where('id', $studentId)->exists()) {
            $student = Student::find($studentId);
        } else {
            return response("Student with given id doesn't exist", 400);
        }
        if (SchoolClass::where('id', $classId)->exists()) {
            $class = SchoolClass::find($classId);
        } else {
            return response("Class with given id doesn't exist", 400);
        }
        // $newStudent = Student::create([
        //     'user_id' => $user->id,
        //     'class_id' => $class->id
        // ]);

        // $classToUpdate = SchoolClass::find($id);

        if ($student && $class) {
            $student->class_id = $class->id;
            $student->save();

            // $class->students()->save($student);
        }
        $message = 'Student %s %s added to class: %s';
        $user = RegisterUser::where('id', $student->user_id)->first();
        return response(sprintf($message, $user->name, $user->surname, $class->name), 200);
    }
}
