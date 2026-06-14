<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Teachers;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (!$teacher) {
            return redirect('/home');
        }

        return view('teacher.dashboard', [
            'pageTitle' => 'Dashboard',
            'teacher' => $teacher
        ]);
    }
}
