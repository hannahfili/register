<?php

namespace App\Http\Controllers;

use App\Helpers\Abilities;
use App\Helpers\Helper;
use App\Models\Activity;
use App\Http\Resources\ActivitiesResource;
use App\Models\RegisterUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use Illuminate\Support\Facades\Hash;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Helper::checkIfUserIsAuthorized($request, Abilities::ACTIVITY_CRUD)) {
                return response()->json(['status' => 401, 'data' => 'Użytkownik nie jest uprawniony do wybranego zasobu'], 401);
            }
            return $next($request);
        });
    }

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
            'conversion_factor' => 'required|numeric|between:1.0,5.0'
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
            // return response('Activity deleted', 200);
            return response()->json(['status' => 200, 'data' => 'Activity deleted'], 200);
        }
        // return response("Activity with given id doesn't exist", 400);
        return response()->json(['status' => 400, 'data' => "Activity with given id doesn't exist"], 400);
    }
}
