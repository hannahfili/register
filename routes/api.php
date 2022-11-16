<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MarksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterUsersController;
use App\Http\Controllers\SclassesController;
use App\Http\Controllers\SubjectController;
use App\Models\RegisterUser;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('users', [RegisterUsersController::class, 'getAll']);
Route::apiResource('users', RegisterUsersController::class);
Route::apiResource('subjects', SubjectController::class);
Route::apiResource('school_classes', SclassesController::class);
Route::apiResource('activities', ActivityController::class);
Route::apiResource('marks', MarksController::class);
Route::apiResource('marks', MarksController::class);
Route::delete('marks/{id}/{moderator_id}', [MarksController::class, 'destroy']);
Route::get('marks/student/{id}', [MarksController::class, 'getStudentMarks']);
Route::get('marks/student/{studentId}/subject/{subjectId}', [MarksController::class, 'getStudentMarksOfParticularSubject']);
Route::post('assign_class_to_subject/{subject_id}/{class_id}', [SubjectController::class, 'assignClass']);
Route::post('assign_teacher_to_subject/{subject_id}/{teacher_id}', [SubjectController::class, 'assignTeacher']);
Route::get('display_subjects_assigned_to_class/{class_id}', [SclassesController::class, 'displaySubjectsAssignedToClass']);

// Route::middleware('auth:api')->get('/users', [RegisterUsersController::class, 'index']);
