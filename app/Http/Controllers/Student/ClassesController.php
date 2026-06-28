<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Students;
use App\Models\Admin\Assignclass;

class ClassesController extends Controller
{
    public function classes()
    {
        $student = Students::find(session('student_id'));

        if (!$student) {
            return redirect('/home');
        }

        $classes = Assignclass::with([
            'teacher',
            'subjects'
        ])
            ->where('semester', $student->current_semester)
            ->get();

        return view('student.classes', [
            'pageTitle' => 'Classes',
            'student' => $student,
            'classes' => $classes
        ]);
    }
}
