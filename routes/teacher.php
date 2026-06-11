<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\LoginController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\StudentsController;
use App\Http\Controllers\Teacher\ClassesController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\ProfileController;

Route::prefix('/teacher')->group(function () {

    Route::get('/', function () {
        return redirect('teacher.dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('teacher.dashboard');

    Route::get('/students', [StudentsController::class, 'students'])
        ->name('teacher.students');

    Route::get('/classes', [ClassesController::class, 'classes'])
        ->name('teacher.classes');

    Route::get('/attendance', [AttendanceController::class, 'attendance'])
        ->name('teacher.attendance');

    Route::get('/profile', [ProfileController::class, 'profile'])
        ->name('teacher.profile');
    
      Route::put('/profile/update/{id}', [ProfileController::class, 'update'])
        ->name('profile.profile');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('teacher.logout');
});