<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\StudentAuthController;
use App\Http\Controllers\Api\TeacherAuthController;

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
});

// Student Routes
Route::post('/student/login', [StudentAuthController::class, 'login']); // Public

Route::group(['prefix' => 'student', 'middleware' => 'auth:sanctum:api-student'], function () {
    // Add protected routes for students here
    Route::post('logout', [StudentAuthController::class, 'logout']);
});

// Teacher Routes
Route::post('/teacher/login', [TeacherAuthController::class, 'login']); // Public

Route::group(['prefix' => 'teacher', 'middleware' => 'auth:sanctum:api-teacher'], function () {
    // Add protected routes for teachers here
    Route::post('logout', [TeacherAuthController::class, 'logout']); // Protected
});
