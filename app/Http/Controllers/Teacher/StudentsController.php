<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Teachers;

class StudentsController extends Controller
{
    public function students()
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (!$teacher) {
            return redirect('/home');
        }

        return view('teacher.students', [
            'pageTitle' => 'Students',
            'teacher' => $teacher
        ]);
    }
}
