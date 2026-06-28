<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Students;

class ReportsController extends Controller
{
    public function reports()
    {
        $student = Students::find(session('student_id'));

        if (!$student) {
            return redirect('/home');
        }

        return view('student.reports', [
            'pageTitle' => 'Reports',
             'student' => $student
        ]);
    }
}
