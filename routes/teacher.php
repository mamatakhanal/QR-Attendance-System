<?php

use Illuminate\Support\Facades\Route;
use App\Models\Admin\AttendanceSession;
use App\Http\Controllers\Teacher\LoginController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\StudentsController;
use App\Http\Controllers\Teacher\AssignclassController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\AttendanceRecordsController;
use App\Http\Controllers\Teacher\ProfileController;

Route::prefix('/teacher')->group(function () {

    Route::get('/', function () {
        return redirect('teacher.dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('teacher.dashboard');

    Route::get('/students', [StudentsController::class, 'students'])
        ->name('teacher.students');

    Route::get('/classes', [AssignclassController::class, 'assignclass'])
        ->name('teacher.assignclass');

    Route::get('/attendance', [AttendanceController::class, 'attendance'])
        ->name('teacher.attendance');

    Route::post('/attendance/start-session', [AttendanceController::class, 'startSession'])
    ->name('teacher.attendance.startSession');

    Route::post('/attendance/create-session', [AttendanceController::class, 'createSession'])
    ->name('teacher.attendance.createSession');

    Route::post('/attendance/scan', [AttendanceController::class, 'scanAttendance'])
    ->name('teacher.attendance.scan');

    Route::post('/attendance/count', [AttendanceController::class, 'getAttendanceCount'])
    ->name('teacher.attendance.count');

    Route::get('/teacher/attendance/pdf', [AttendanceRecordsController::class,'downloadPdf'])       
        ->name('teacher.attendance.pdf');

    Route::get('/attendance-records', [AttendanceRecordsController::class, 'attendancerecords'])
        ->name('teacher.attendancerecords');


    // Profile
    Route::get('/profile', [ProfileController::class, 'profile'])
        ->name('teacher.profile');

    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])
        ->name('profile.profile.update');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('teacher.logout');
});
