<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisterUser;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;

use App\Http\Resources\RegisterUserResource;
use App\Http\Resources\RegisterUserCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\isEmpty;

class RegisterUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RegisterUserResource::collection(RegisterUser::all());
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
            'surname' => 'required|string|max:199',
            'email' => 'required|email|unique:register_users,email',
            'password' => 'required',
            'isAdmin' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if ($request->isTeacher == true) {
            $validator = Validator::make($request->all(), [
                'subject_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }
        }
        if ($request->isStudent == true) {
            $validator = Validator::make($request->all(), [
                'class_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }
        }

        $newUser = RegisterUser::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'isAdmin' => $request->isAdmin
        ]);

        if ($request->isTeacher == true) {
            $newTeacher = Teacher::create([
                'user_id' => $newUser->id,
                'subject_id' => $request->subject_id
            ]);
        }
        if ($request->isStudent == true) {
            $newStudent = Student::create([
                'user_id' => $newUser->id,
                'class_id' => $request->class_id
            ]);
        }


        // return response()->json(['Created.', new RegisterUserResource($newUser)]);
        return response(new RegisterUserResource($newUser), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RegisterUser
     * @return \Illuminate\Http\Response
     */
    public function show(RegisterUser $user)
    {
        return new RegisterUserResource($user);
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
        // $userToUpdate = null;
        if (count($request->all()) == 0) {
            return response('Nothing data given to update', 400);
        }
        if (RegisterUser::where('id', $id)->exists()) {
            $userToUpdate = RegisterUser::find($id);
        } else {
            return response("User with given id doesn't exist", 400);
        }

        if ($request->has('name')) {
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:199'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }
            $userToUpdate->name = $request->name;
        }
        if ($request->has('surname')) {
            $validator = Validator::make($request->all(), [
                'surname' => 'string|max:199'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }
            $userToUpdate->surname = $request->surname;
        }
        if ($request->has('email')) {
            $validator = Validator::make($request->all(), [
                'email' => 'email'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }
            $userWithEmail = RegisterUser::where('email', $request->email)->first();
            if ($userWithEmail === null) {
                $userToUpdate->email = $request->email;
            } else {
                if ($userWithEmail->id === $id) {
                    return response('New email has to be different than already existing', 400);
                } else {
                    return response('Email already exists', 400);
                }
            }
        }
        if ($request->has('password')) {
            $userToUpdate->password = Hash::make($request->password);
        }
        if ($request->has('isAdmin')) {
            $userToUpdate->isAdmin = $request->isAdmin;
        }
        $userToUpdate->save();

        if ($request->isTeacher == true && $request->has('subject_id')) {
            $teacherToUpdate = Teacher::where('user_id', $id)->first();
            if ($teacherToUpdate !== null) {
                $teacherToUpdate->subject_id = $request->subject_id;
                $teacherToUpdate->save();
            } else {
                return response('Teacher id not found', 400);
            }
        }
        if ($request->isStudent == true && $request->has('class_id')) {
            $studentToUpdate = Student::where('user_id', $id)->first();
            if ($studentToUpdate !== null) {
                $studentToUpdate->class_id = $request->class_id;
                $studentToUpdate->save();
            } else {
                return response('Student id not found', 400);
            }
        }
        // echo $userToUpdate;


        return response(new RegisterUserResource($userToUpdate), 201);
        // return response($userToUpdate, 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (RegisterUser::where('id', $id)->exists()) {
            $userToDelete = RegisterUser::find($id);
            $userToDelete->delete();
            return response('RegisterUser deleted', 200);
        }
        return response('User with given id is not found', 400);
    }
}
