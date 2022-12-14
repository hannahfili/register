<?php

namespace App\Http\Controllers;

use App\Http\Resources\SclassesResource;
use App\Models\Sclass;
use App\Models\RegisterUser;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SclassesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SclassesResource::collection(Sclass::all());
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
        $newClass = Sclass::create([
            'name' => $request->name,
            'class_start' => $request->class_start,
            'class_end' => $request->class_end
        ]);
        return response(new SclassesResource($newClass), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Sclass $schoolClass)
    {
        return new SclassesResource($schoolClass);
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
        if (Sclass::where('id', $id)->exists()) {
            $classToUpdate = Sclass::find($id);
        } else {
            return response()->json(['status' => 404, 'data' => 'Klasa o podanym ID nie istnieje'], 404);
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
        return response(new SclassesResource($classToUpdate), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Sclass::where('id', $id)->exists()) {
            $classToDelete = Sclass::find($id);
            $classToDelete->delete();
            return response()->json(['status' => 200, 'data' => 'Klasa usunięta pomyślnie'], 200);
        }
        return response()->json(['status' => 404, 'data' => 'Klasa o podanym ID nie istnieje'], 404);
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
            return response()->json(['status' => 404, 'data' => 'Student o podanym ID nie istnieje'], 404);
        }
        if (Sclass::where('id', $classId)->exists()) {
            $class = Sclass::find($classId);
        } else {
            return response()->json(['status' => 404, 'data' => 'Klasa o podanym ID nie istnieje'], 404);
        }

        if ($student && $class) {
            $student->sclass_id = $class->id;
            $student->save();

        }
        $message = 'Student %s %s added to class: %s';
        $user = RegisterUser::where('id', $student->user_id)->first();
        return response()->json(['status' => 200, 'data' => sprintf($message, $user->name, $user->surname, $class->name)], 200);
    }
    /**
     * Update the specified resource in storage.
     * @param  int  $classId
     * @return \Illuminate\Http\Response
     */
    public function displaySubjectsAssignedToClass($classId)
    {
        if (!Sclass::where('id', $classId)->exists()) {
            return response()->json(['status' => 404, 'data' => 'Klasa o podanym ID nie istnieje'], 404);
        }
        $class = Sclass::where('id', $classId)->first();
        return response()->json(['status' => 200, 'data' => $class->subjects], 200);
    }
    public function getClassesAssignedToThisTeacher($subjectId)
    {
        if (!Subject::where('id', $subjectId)->exists()) {
            return response()->json(['status' => 404, 'data' => "Przedmiot o podanym ID nie istnieje"], 404);
        }
        $classes = array();

        $schoolClassesSubjects = DB::table('sclass_subject')->where('subject_id', $subjectId)->get();

        foreach ($schoolClassesSubjects as $sss) {
            $classInfo = Sclass::where('id', $sss->sclass_id)->first();
            array_push($classes, $classInfo);
        }

        return response()->json(['status' => 200, 'data' => $classes], 200);
    }
    public function getClassAssignedToThisStudent($student_userId)
    {
        if (!Student::where('user_id', $student_userId)->exists()) {
            return response()->json(['status' => 404, 'data' => 'Użytkownik o podanym ID nie jest uczniem'], 404);
        }
        $student = Student::where('user_id', $student_userId)->first();
        if ($student->sclass_id == null) {
            return response()->json(['status' => 404, 'data' => 'Uczeń nie jest przypisany do żadnej klasy'], 404);
        }
        $sclass = Sclass::where('id', $student->sclass_id)->first();
        return response()->json(['status' => 200, 'data' => new SclassesResource($sclass)], 200);
    }
}
