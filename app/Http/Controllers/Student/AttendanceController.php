<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{

    public function attendance()
    {
        return view('student.attendance', [
            'pageTitle' => 'Attendance'
        ]);
    }
}