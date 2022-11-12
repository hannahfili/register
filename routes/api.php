<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterUsersController;
use App\Http\Controllers\SchoolClassesController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::get('users', [RegisterUsersController::class, 'getAll']);
Route::apiResource('users', RegisterUsersController::class);
Route::apiResource('school_classes', SchoolClassesController::class);
Route::post('assign_student_to_class/{class_id}/{student_id}', [SchoolClassesController::class, 'assignStudent']);
