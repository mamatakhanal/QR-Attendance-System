<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Students;

class TeachersController extends Controller
{
    public function teachers()
    {
        $student = Students::find(session('student_id'));

        if (!$student) {
            return redirect('/home');
        }

        return view('student.teachers', [
            'pageTitle' => 'Teachers',
            'student' => $student
        ]);
    }
}
