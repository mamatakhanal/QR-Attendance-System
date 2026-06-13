<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mainpage\HomeController;

use App\Http\Controllers\Mainpage\LoginController;

Route::get('/home', [HomeController::class, 'home']);
Route::get('/', [HomeController::class, 'home'])
    ->name('mainpage.home');

Route::post('/home/teacher/login', [LoginController::class, 'login']);

Route::get('/teacher/dashboard', function () {
    return view('teacher.dashboard');
});

Route::get('/student/dashboard', function () {
    return view('student.dashboard');
});

// Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::post('/teacher-login', [LoginController::class, 'teacherLogin'])
->name('teacher.login');


Route::post('/student-login', [LoginController::class, 'studentLogin'])
->name('student.login');