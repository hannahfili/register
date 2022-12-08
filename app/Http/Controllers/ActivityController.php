<?php

namespace App\Http\Controllers;

use App\Helpers\Abilities;
use App\Models\Activity;
use App\Http\Resources\ActivitiesResource;
use App\Models\RegisterUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;

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
        // echo $request->bearerToken();
        $token = $request->bearerToken();
        // if (!PersonalAccessToken::where('token', $token)->exists()) {
        //     return response('Not authorized', 404);
        // }
        // $hania = hash('sha256', 'hania');
        // echo $hania;

        // echo 'hania';

        // echo $token;
        // $tokeny = PersonalAccessToken::all();
        $tokenHashed = hash('sha256', $token);

        echo $tokenHashed;
        // echo $tokenHashed;
        // $tokenHashed = hash('sha256', $token);
        // echo $tokenHashed;
        $tokenX = PersonalAccessToken::where('token', $tokenHashed)->first();

        // echo $tokeny;
        echo $tokenX;

        $user_id = $tokenX->tokenable_id;

        $userWithEmail = RegisterUser::where('id', $user_id)->first();
        echo $userWithEmail;
        $moznosc = Abilities::ACTIVITY_CRUD;
        echo $moznosc;
        if ($userWithEmail->tokenCan('ACTIVITY_CRUD')) {
            echo 'MOGE';
        } else {
            echo 'NIE MOGE';
        }

        $abilities = $tokenX->abilities;
        if (in_array($moznosc, $abilities)) {
            echo 'MOGEMOGEMOGE';
            echo $moznosc;
        }

        // echo $tokenX;
        // $user = $tokenX->tokenable;
        // echo $user;
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
            // return response('Activity deleted', 200);
            return response()->json(['status' => 200, 'data' => 'Activity deleted'], 200);
        }
        // return response("Activity with given id doesn't exist", 400);
        return response()->json(['status' => 400, 'data' => "Activity with given id doesn't exist"], 400);
    }
}
