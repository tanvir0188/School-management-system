<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/add-teacher', function () {
        return view('admin.register-teacher');
    })->name('add-teacher');
    Route::get('/teachers', function () {
        return view('admin.teachers');
    })->name('teachers');

    Route::get('/add-student', function () {
        return view('admin.register-student');
    })->name('add-student');
    Route::get('/students', function () {
        return view('admin.students');
    })->name('students');

    Route::get('/create-class', function () {
        return view('admin.create-class');
    })->name('create-class');

    Route::get('/create-notice', function () {
        return view('admin.create-notice');
    })->name('create-notice');

    Route::get('/create-section', function () {
        return view('admin.create-section');
    })->name('create-section');
    Route::get('/create-exam-type', function () {
        return view('admin.create-exam-type');
    })->name('create-exam-type');
    Route::get('/create-exam', function () {
        return view('admin.create-exam');
    })->name('create-exam');
    Route::get('/create-result', function () {
        return view('admin.create-result');
    })->name('create-result');
    Route::get('/exams', function () {
        return view('admin.exams');
    })->name('exams');
    Route::get('/notices', function () {
        return view('admin.notices');
    })->name('notices');
});


Route::get('/admin-sign-in', function () {
    return view('public.admin-sign-in');
})->name('admin-sign-in');
