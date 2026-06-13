<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\LoginController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\TeachersController;
use App\Http\Controllers\Student\ClassesController;
use App\Http\Controllers\Student\AttendanceController;
use App\Http\Controllers\Student\ProfileController;

Route::prefix('/student')->group(function () {

    Route::get('/', function () {
        return redirect('student.dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('student.dashboard');

    Route::get('/teachers', [TeachersController::class, 'teachers'])
        ->name('student.teachers');

    Route::get('/classes', [ClassesController::class, 'classes'])
        ->name('student.classes');

    Route::get('/attendance', [AttendanceController::class, 'attendance'])
        ->name('student.attendance');

    Route::get('/profile', [ProfileController::class, 'profile'])
        ->name('student.profile');

    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])
        ->name('student.profile.update');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('student.logout');
});
