<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::group(['prefix' => 'admin', 'middleware' => 'auth:sanctum:api-admin'], function () {
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('register', [AdminAuthController::class, 'register']);

    Route::post('/register/student', [AdminAuthController::class, 'studentRegister']);
    Route::post('/register/teacher', [AdminAuthController::class, 'teacherRegister']);
});
