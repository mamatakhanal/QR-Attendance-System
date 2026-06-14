<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Teachers;

class AttendanceController extends Controller
{
    public function attendance()
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (!$teacher) {
            return redirect('/home');
        }
        return view('teacher.attendance', [
            'pageTitle' => 'Attendance',
            'teacher' => $teacher
        ]);
    }
}
