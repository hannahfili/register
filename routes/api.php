<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MarkModificationsController;
use App\Http\Controllers\MarksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterUsersController;
use App\Http\Controllers\SclassesController;
use App\Http\Controllers\SubjectController;
use App\Models\RegisterUser;
use App\Models\Sclass;

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

Route::apiResource('users', RegisterUsersController::class)->middleware('UserCRUD');
Route::apiResource('subjects', SubjectController::class)->middleware('SubjectCRUD');
Route::apiResource('school_classes', SclassesController::class)->middleware('SchoolClassCRUD');
Route::apiResource('activities', ActivityController::class)->middleware('ActivityCRUD');
Route::get('get_user_by_id/{id}', [RegisterUsersController::class, 'getUserById'])->middleware('UserREAD');

Route::apiResource('marks', MarksController::class)->middleware('MarkREAD');
Route::delete('marks/{id}/{moderator_id}', [MarksController::class, 'deleteMarkAndCreateMarkModification'])->middleware('MarkCRUD');
Route::get('marks/student/{id}', [MarksController::class, 'getStudentMarks'])->middleware('MarkREAD');
Route::get('marks/student/{userStudentId}/subject/{subjectId}', [MarksController::class, 'getStudentMarksOfParticularSubject'])->middleware('MarkREAD');
Route::get('student/{student_id}/assigned_subjects', [RegisterUsersController::class, 'getSubjectsAssignedToThisStudent'])->middleware('SubjectREAD');
Route::get('marks/class/{classId}/subject/{subjectId}', [MarksController::class, 'getClassMarksOfParticularSubjectDividedByStudents']);

Route::apiResource('marks_modifications', MarkModificationsController::class)->middleware('MarkModificationCRUD');
// Route::get('marks_modifications', [MarkModificationsController::class, 'index']);
Route::get('marks_modifications/student/{user_student_id}', [MarkModificationsController::class, 'getMarksModificationsOfParticularUserStudent']);
Route::get('marks_modifications/class/{classId}/subject/{subjectId}', [MarkModificationsController::class, 'getClassMarksModificationsOfParticularSubjectDividedByStudents'])->middleware('MarkModificationCRUD');
Route::get('marks_modifications/student/{userStudentId}/subject/{subjectId}', [MarkModificationsController::class, 'getStudentMarksModificationsOfParticularSubject'])->middleware('MarkREAD');

Route::post('assign_class/{class_id}/to_subject/{subject_id}', [SubjectController::class, 'assignClassToSubject']);
Route::post('assign_subject_to_teacher/{subject_id}/{teacher_id}', [SubjectController::class, 'assignTeacher']);
Route::get('display_subjects_assigned_to_class/{class_id}', [SclassesController::class, 'displaySubjectsAssignedToClass']);
Route::post('assign_student_to_class/{class_id}/{student_id}', [SclassesController::class, 'assignStudent']);
Route::post('discharge_student_from_class/{student_id}', [RegisterUsersController::class, 'dischargeStudentFromClass']);

Route::get('students/getAll', [RegisterUsersController::class, 'getAllStudents']);
Route::get('students/not_assigned', [RegisterUsersController::class, 'getStudentsNotAssignedToAnyClass']);
// Route::middleware('auth:api')->get('/users', [RegisterUsersController::class, 'index']);

Route::get('subject/{subject_id}/classes', [SubjectController::class, 'displayClassesAssignedToThisSubject']);
Route::get('subject/{subject_id}/not_assigned_classes', [SubjectController::class, 'displayClassesNotAssignedToThisSubject']);


Route::get('teachers/not_assigned_to_any_subject', [RegisterUsersController::class, 'getTeachersNotAssignedToAnySubject']);

Route::get('subject/{subject_id}/assigned_teacher', [RegisterUsersController::class, 'getTeacherAssignedToThisSubject']);

Route::post('subject/{subject_id}/discharge_class/{class_id}', [SubjectController::class, 'dischargeClassFromSubject']);

Route::get('/teacher/{teacher_user_id}/get_subject_assigned', [RegisterUsersController::class, 'getSubjectAssignedToThisTeacher']);

Route::post('/login', [RegisterUsersController::class, 'logIn']);
Route::post('/logout', [RegisterUsersController::class, 'logOut']);
Route::post('/user_assigned_to_token', [RegisterUsersController::class, 'getUserAssignedToToken']);

Route::get('/teacher/subject/{subjectId}/get_classes', [SclassesController::class, 'getClassesAssignedToThisTeacher']);
Route::get('/student/{studentUserId}/get_class', [SclassesController::class, 'getClassAssignedToThisStudent']);

Route::get('/user/{userId}/get_student_id', [RegisterUsersController::class, 'getUserStudentId']);
Route::get('/user/{userId}/get_teacher_id', [RegisterUsersController::class, 'getUserTeacherId']);
