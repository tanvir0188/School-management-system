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
});


Route::get('/admin-sign-in', function () {
    return view('public.admin-sign-in');
})->name('admin-sign-in');
