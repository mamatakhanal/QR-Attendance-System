<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Students;

class AttendanceController extends Controller
{

    public function attendance()
    {
        $student = Students::find(session('student_id'));

        if (!$student) {
            return redirect('/home');
        }

        return view('student.attendance', [
            'pageTitle' => 'Attendance',
            'student' => $student
        ]);
    }
}
