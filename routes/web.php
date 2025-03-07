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
    Route::get('/exams-type-management-with-result', function () {
        return view('admin.exams-type-management-with-result');
    })->name('exams-type-management-with-result');
    Route::get('/exams', function () {
        return view('admin.exams');
    })->name('exams');
    Route::get('/notices', function () {
        return view('admin.notices');
    })->name('notices');
    Route::get('/results', function () {
        return view('admin.results');
    })->name('results');

    Route::get('/section/{class}/{class_id}/{section}/{sec_id}', function () {
        return view('admin.manage-section');
    })->name('manage-section');
});


Route::get('/admin-sign-in', function () {
    return view('public.admin-sign-in');
})->name('admin-sign-in');





Route::get('/student-login', function () {
    return view('public.student-login');
})->name('student-login');

Route::prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', function () {
        return view('student.dashboard');
    })->name('dashboard');
    Route::get('/profile', function () {
        return view('student.profile');
    })->name('profile');
    Route::get('/create-profile', function () {
        return view('student.create-profile');
    })->name('create-profile');
    Route::get('/update-profile', function () {
        return view('student.update-profile');
    })->name('update-profile');
    Route::get('/teachers', function () {
        return view('student.teachers');
    })->name('teachers');
    Route::get('/notices', function () {
        return view('student.notices');
    })->name('notices');
    Route::get('/section-students', function () {
        return view('student.section-students');
    })->name('section-students');
    Route::get('/exams', function () {
        return view('student.exams');
    })->name('exams');
});


Route::get('/teacher-login', function () {
    return view('public.teacher-login');
})->name('teacher-login');

Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', function () {
        return view('teacher.dashboard');
    })->name('dashboard');
    Route::get('/teachers', function () {
        return view('teacher.teachers');
    })->name('teachers');

    Route::get('/notices', function () {
        return view('teacher.notices');
    })->name('notices');
    Route::get('/create-profile', function () {
        return view('teacher.create-profile');
    })->name('create-profile');
    Route::get('/profile', function () {
        return view('teacher.profile');
    })->name('profile');
    Route::get('/update-profile', function () {
        return view('teacher.update-profile');
    })->name('update-profile');

    Route::get('/section-students/{class}/{section}/{id}', function () {
        return view('teacher.section-students');
    })->name('section-students');
});
