<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Http\Resources\ActivitiesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ActivitiesResource::collection(Activity::all());
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
            'name' => 'required|string|unique:activities|max:199',
            'conversion_factor' => 'required|numeric|between:0.0,1.0'
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }
        $newActivity = Activity::create([
            'name' => $request->name,
            'conversion_factor' => $request->conversion_factor,
        ]);
        return response($newActivity, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        return new ActivitiesResource($activity);
    }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Activity::where('id', $id)->exists()) {
            $activityToDelete = Activity::find($id);
            $activityToDelete->delete();
            return response('Activity deleted', 200);
        }
        return response("Activity with given id doesn't exist", 400);
    }
}
