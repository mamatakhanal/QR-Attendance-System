<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Teachers;

class ClassesController extends Controller
{
    public function classes()
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (!$teacher) {
            return redirect('/home');
        }

        return view('teacher.classes', [
            'pageTitle' => 'Assign Classes',
            'teacher' => $teacher
        ]);
    }
}
