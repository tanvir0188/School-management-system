<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\StudentAuthController;
use App\Http\Controllers\Api\StudentProfileController;
use App\Http\Controllers\Api\TeacherAuthController;
use App\Models\StudentProfile;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
// Route::group(['prefix' => 'admin', 'middleware' => 'auth:sanctum:api-admin'], function () {
//     Route::post('login', [AdminAuthController::class, 'login']);
//     Route::post('register', [AdminAuthController::class, 'register']);

//     Route::post('/register/student', [AdminAuthController::class, 'studentRegister']);
//     Route::post('/register/teacher', [AdminAuthController::class, 'teacherRegister']);
// });

// Route::group(['prefix' => 'student', 'middleware' => 'auth:sanctum:api-student'], function () {
//     Route::post('login', [StudentAuthController::class, 'login']);
// });

// Route::group(['prefix' => 'teacher', 'middleware' => 'auth:sanctum:api-teacher'], function () {
//     Route::post('login', [TeacherAuthController::class, 'login']);
// });
// Admin Routes
Route::post('/admin/login', [AdminAuthController::class, 'login']); // Public
Route::post('/admin/register', [AdminAuthController::class, 'register']); // Public

Route::group(['prefix' => 'admin', 'middleware' => 'auth:sanctum:api-admin'], function () {
    Route::post('/register/student', [AdminAuthController::class, 'studentRegister']);
    Route::post('/register/teacher', [AdminAuthController::class, 'teacherRegister']);
    Route::get('/teacher/index', [AdminAuthController::class, 'teachers']);
    Route::get('/teacher/{id}', [AdminAuthController::class, 'showTeacher']);
    Route::delete('/teacher/{id}', [AdminAuthController::class, 'deleteTeacher']);
    Route::delete('/student/{id}', [AdminAuthController::class, 'deleteStudent']);
    Route::get('/student/index', [AdminAuthController::class, 'students']);
    Route::get('/student/{id}', [AdminAuthController::class, 'showStudent']);
    Route::get('studentProfile/index', [StudentProfileController::class, 'index']);
    Route::get('studentProfile/{id}', [StudentProfileController::class, 'show']);
});

// Student Routes
Route::post('/student/login', [StudentAuthController::class, 'login']); // Public

Route::group(['prefix' => 'student', 'middleware' => 'auth:sanctum:api-student'], function () {
    // Add protected routes for students here
    Route::post('logout', [StudentAuthController::class, 'logout']);
    Route::post('studentProfile/store', [StudentProfileController::class, 'store']);
    Route::get('studentProfile/index', [StudentProfileController::class, 'index']);
    Route::get('studentProfile/{id}', [StudentProfileController::class, 'show']);
    Route::put('studentProfile/{id}', [StudentProfileController::class, 'update']);
});

// Teacher Routes
Route::post('/teacher/login', [TeacherAuthController::class, 'login']); // Public

Route::group(['prefix' => 'teacher', 'middleware' => 'auth:sanctum:api-teacher'], function () {
    // Add protected routes for teachers here
    Route::post('logout', [TeacherAuthController::class, 'logout']); // Protected
    Route::get('/student/index', [TeacherAuthController::class, 'students']);
    Route::get('/student/{id}', [TeacherAuthController::class, 'showStudent']);
    Route::post('studentProfile/index', [StudentProfileController::class, 'index']);
    Route::get('studentProfile/{id}', [StudentProfileController::class, 'show']);
});
