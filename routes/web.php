<?php

use Illuminate\Support\Facades\Route;


Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin-sign-in', function () {
    return view('public.admin-sign-in');
})->name('admin-sign-in');
