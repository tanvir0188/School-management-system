<?php

use Illuminate\Http\Request;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\ExamTypeController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\ExamResultController;

use App\Http\Controllers\Api\StudentAuthController;
use App\Http\Controllers\Api\TeacherAuthController;
use App\Http\Controllers\Api\SectionNoticeController;
use App\Http\Controllers\Api\StudentProfileController;
use App\Http\Controllers\Api\TeacherProfileController;

// Admin Routes
Route::post('/admin/login', [AdminAuthController::class, 'login']); // Public
Route::post('/admin/register', [AdminAuthController::class, 'register']); // Public
Route::get('teacherProfile/index', [TeacherProfileController::class, 'index']);
Route::get('studentProfile/index', [StudentProfileController::class, 'index']);
Route::get('studentProfile/{id}', [StudentProfileController::class, 'show']);
Route::get('teacherProfile/{id}', [TeacherProfileController::class, 'show']);
Route::get('notice', [NoticeController::class, 'index']);
Route::get('notice/{id}', [NoticeController::class, 'show']);

Route::get('section-notice', [SectionNoticeController::class, 'index']);
Route::get('section-notice/{id}', [SectionNoticeController::class, 'show']);

Route::get('exam-type', [ExamTypeController::class, 'index']);
Route::get('exam-type/{id}', [ExamTypeController::class, 'show']);

Route::get('exam', [ExamController::class, 'index']);
Route::get('exam/{id}', [ExamController::class, 'show']);

Route::get('exam-result', [ExamResultController::class, 'index']);
Route::get('exam-result/{id}', [ExamResultController::class, 'show']);
Route::get('class/index', [ClassController::class, 'index']);
Route::get('section/index-by-class/{id}', [SectionController::class, 'sectionByClass']);

Route::group(['prefix' => 'admin', 'middleware' => 'auth:sanctum:api-admin'], function () {
    Route::post('/register/student', [AdminAuthController::class, 'studentRegister']);
    Route::post('/register/teacher', [AdminAuthController::class, 'teacherRegister']);
    Route::post('logout', [AdminAuthController::class, 'logout']);
    Route::get('/teacher/index', [AdminAuthController::class, 'teachers']);
    Route::get('/teacher-without-pagination/index', [AdminAuthController::class, 'teachersWithoutPagination']);

    Route::get('/teacher/{id}', [AdminAuthController::class, 'showTeacher']);
    Route::delete('/teacher/{id}', [AdminAuthController::class, 'deleteTeacher']);
    Route::delete('/student/{id}', [AdminAuthController::class, 'deleteStudent']);
    Route::get('/student/index', [AdminAuthController::class, 'students']);
    Route::get('/student/{id}', [AdminAuthController::class, 'showStudent']);
    Route::get('studentProfile/{id}', [StudentProfileController::class, 'show']);


    Route::post('class/store', [ClassController::class, 'store']);
    Route::delete('class/{id}', [ClassController::class, 'destroy']);

    Route::get('section/index', [SectionController::class, 'index']);
    Route::post('section/store', [SectionController::class, 'store']);
    Route::put('section/update/{id}', [SectionController::class, 'update']);
    Route::delete('section/{id}', [SectionController::class, 'destroy']);


    Route::post('notice', [NoticeController::class, 'store']);
    Route::put('notice/{id}', [NoticeController::class, 'update']);
    Route::delete('notice/{id}', [NoticeController::class, 'destroy']);

    Route::post('exam-type', [ExamTypeController::class, 'store']);
    Route::put('exam-type/{id}', [ExamTypeController::class, 'update']);
    Route::delete('exam-type/{id}', [ExamTypeController::class, 'destroy']);

    Route::post('exam', [ExamController::class, 'store']);
    Route::put('exam/{id}', [ExamController::class, 'update']);
    Route::delete('exam/{id}', [ExamController::class, 'destroy']);

    Route::post('exam-result', [ExamResultController::class, 'store']);
    Route::put('exam-result/{id}', [ExamResultController::class, 'update']);
    Route::delete('exam-result/{id}', [ExamResultController::class, 'destroy']);
});

// Student Routes
Route::post('/student/login', [StudentAuthController::class, 'login']); // Public

Route::group(['prefix' => 'student', 'middleware' => 'auth:sanctum:api-student'], function () {
    // Add protected routes for students here
    Route::post('logout', [StudentAuthController::class, 'logout']);
    Route::post('studentProfile/store', [StudentProfileController::class, 'store']);
    Route::put('studentProfile/{id}', [StudentProfileController::class, 'update']);
});

// Teacher Routes
Route::post('/teacher/login', [TeacherAuthController::class, 'login']); // Public

Route::group(['prefix' => 'teacher', 'middleware' => 'auth:sanctum:api-teacher'], function () {
    // Add protected routes for teachers here
    Route::post('logout', [TeacherAuthController::class, 'logout']); // Protected
    Route::get('/student/index', [TeacherAuthController::class, 'students']);
    Route::get('/student/{id}', [TeacherAuthController::class, 'showStudent']);
    Route::post('teacherProfile/store', [TeacherProfileController::class, 'store']);
    Route::put('teacherProfile/{id}', [TeacherProfileController::class, 'update']);

    Route::post('section-notice', [SectionNoticeController::class, 'store']);
    Route::put('section-notice/{id}', [SectionNoticeController::class, 'update']);
    Route::delete('section-notice/{id}', [SectionNoticeController::class, 'destroy']);
});
