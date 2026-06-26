<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Teachers;
use App\Models\Admin\Assignclass;
use Illuminate\Http\Request;

class AssignclassController extends Controller
{
    public function assignclass(Request $request)
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (!$teacher) {
            return redirect('/home');
        }

        return view('teacher.assignclass', [
            'pageTitle' => 'Assign Classes',
            'teacher' => $teacher
        ]);
    }
}
