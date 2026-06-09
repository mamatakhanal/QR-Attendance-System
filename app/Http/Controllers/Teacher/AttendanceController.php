<?php
namespace App\Http\Controllers\Teacher;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function attendance()
    {
        return view('teacher.attendance', [
            'pageTitle' => 'Attendance'
        ]);
    }
}