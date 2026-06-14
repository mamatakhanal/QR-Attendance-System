<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Students;

class ClassesController extends Controller
{
    public function classes()
    {
        $student = Students::find(session('student_id'));

        if (!$student) {
            return redirect('/home');
        }

        return view('student.classes', [
            'pageTitle' => 'Classes',
            'student' => $student
        ]);
    }
}
