<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TeachersController;
use App\Http\Controllers\Admin\StudentsController;
use App\Http\Controllers\Admin\ClassesController;
use App\Http\Controllers\Admin\SubjectsController;
use App\Http\Controllers\Admin\AssignclassController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ProfileController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


Route::prefix('/admin')->group(function () {

    Route::get('/', function () {
        return redirect('/admin/login');
    });

    // Login route
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLogin')->name('admin.login');
        Route::post('/login', 'loginCheck')->name('admin.login.post');
        Route::post('/logout', 'logout')->name('admin.logout');
    });

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('admin.dashboard');



    // Teachers
    Route::get('/teachers', [TeachersController::class, 'teachers'])
        ->name('admin.teachers');

    Route::post('/teachers/create', [TeachersController::class, 'create'])
        ->name('teachers.create');

    Route::put('/teachers/update/{id}', [TeachersController::class, 'update'])
        ->name('teachers.update');

    Route::delete('/teachers/{id}', [TeachersController::class, 'delete'])
        ->name('teachers.delete');

    Route::get('/teacher/send-email/{id}', [TeachersController::class, 'sendEmail']);



    // Students
    Route::get('/students', [StudentsController::class, 'students'])
        ->name('admin.students');

    Route::post('/students/create', [StudentsController::class, 'create'])
        ->name('students.create');

    Route::put('/students/update/{id}', [StudentsController::class, 'update'])
        ->name('students.update');

    Route::delete('/students/{id}', [StudentsController::class, 'delete'])
        ->name('students.delete');

    Route::get('/student-qr/{code}', function ($code) {
        return response(
            QrCode::format('svg')
                ->size(250)
                ->generate($code)
        )->header('Content-Type', 'image/svg+xml');
    });

    Route::get('/student/send-email/{id}', [StudentsController::class, 'sendEmail']);


    Route::get('/classes', [ClassesController::class, 'classes'])
        ->name('admin.classes');

    Route::get('/subjects', [SubjectsController::class, 'subjects'])
        ->name('admin.subjects');

    Route::get('/assignclass', [AssignclassController::class, 'assignclass'])
        ->name('admin.assignclass');

    Route::get('/attendance', [AttendanceController::class, 'attendance'])
        ->name('admin.attendance');

    Route::get('/profile', [ProfileController::class, 'profile'])
        ->name('admin.profile');
        
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])
        ->name('sadmin.update');
});
