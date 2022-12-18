<?php

namespace App\Http\Controllers;

use App\Http\Resources\SclassesResource;
use App\Http\Resources\SubjectsResource;
use App\Models\Sclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Subject;
use App\Models\SclassesSubjects;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

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
        return response()->json(['status' => 200, 'data' => $newSubject], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $subjectId
     * @param  int  $classId
     * @return \Illuminate\Http\Response
     */
    public function assignClassToSubject($classId, $subjectId)
    {
        if (!Subject::where('id', $subjectId)->exists()) return response()->json(['status' => 404, 'data' => 'Przedmiot szkolny o podanym ID nie istnieje'], 404);
        if (!Sclass::where('id', $classId)->exists()) return response()->json(['status' => 404, 'data' => 'Klasa o podanym ID nie istnieje'], 404);
        $subject = Subject::where('id', $subjectId)->first();
        $class = Sclass::where('id', $classId)->first();

        if ($subject->sclasses()->wherePivot('sclass_id', '=', $class->id)->exists()) {
            return response()->json(['status' => 400, 'data' => 'Ta klasa została już przypisana do przedmioty'], 400);
        }


        $subject->sclasses()->attach($class);
        // return response('Class assigned properly', 200);
        return response()->json(['status' => 200, 'data' => 'Klasa przypisana pomyślnie do przedmiotu'], 200);
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
        if (!Subject::where('id', $subjectId)->exists()) return response()->json(['status' => 404, 'data' => 'Przedmiot szkolny o podanym ID nie istnieje'], 404);
        if (!Teacher::where('id', $teacherId)->exists()) return response()->json(['status' => 404, 'data' => 'Nauczyciel o podanym ID nie istnieje'], 404);
        $subject = Subject::where('id', $subjectId)->first();
        $teacher = Teacher::where('id', $teacherId)->first();

        $teacher->subject_id = $subject->id;
        $teacher->save();

        return response()->json(['status' => 200, 'data' => 'Nauczyciel został pomyślnie przypisany do przedmiotu'], 200);
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
            return response()->json(['status' => 400, 'data' => 'Nie wysłano żadnych danych'], 400);
        }
        if (Subject::where('id', $id)->exists()) {
            $subjectToUpdate = Subject::find($id);
        } else {
            return response()->json(['status' => 404, 'data' => 'Przedmiot szkolny o podanym ID nie istnieje'], 404);
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
            return response()->json(['status' => 200, 'data' => 'Przedmiot szkolny usunięty pomyślnie'], 200);
        }
        return response()->json(['status' => 404, 'data' => 'Przedmiot szkolny o podanym ID nie istnieje'], 404);
    }

    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function displayClassesAssignedToThisSubject($id)
    {

        if (Subject::where('id', $id)->exists()) {
            $classes = Subject::find($id)->sclasses()->get();
            return response()->json(['status' => 200, 'data' => SclassesResource::collection(($classes))], 200);
        }
        return response()->json(['status' => 404, 'data' => 'Przedmiot szkolny o podanym ID nie istnieje'], 404);
    }
    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function displayClassesNotAssignedToThisSubject($id)
    {
        $all_classes = Sclass::all();
        $not_assigned_classes = array();
        if (!Subject::where('id', $id)->exists()) {
            return response()->json(['status' => 400, 'data' => 'Przedmiot o podanym ID nie istnieje'], 400);
        }

        foreach ($all_classes as $school_class) {
            if (count($school_class->subjects()->where('subject_id', $id)->get()->toArray()) == 0) {
                array_push($not_assigned_classes, $school_class);
            }
        }
        return response()->json(['status' => 200, 'data' => SclassesResource::collection(($not_assigned_classes))], 200);
    }
    /**
     *
     * @param  int  $subjectId
     * @param  int  $classId
     * @return \Illuminate\Http\Response
     */
    public function dischargeClassFromSubject($subjectId, $classId)
    {
        if (DB::table('sclass_subject')
            ->where('subject_id', $subjectId)
            ->where('sclass_id', $classId)->exists()
        ) {
            $class_subject_record = DB::table('sclass_subject')
                ->where('subject_id', $subjectId)
                ->where('sclass_id', $classId)->delete();
            return response()->json(['status' => 200, 'data' => 'Klasa odpięta od przedmiotu pomyślnie!'], 200);
        }
        return response()->json(['status' => 400, 'data' => 'Nie istnieje powiązanie między wybraną klasą i przedmiotem'], 400);
    }
}
