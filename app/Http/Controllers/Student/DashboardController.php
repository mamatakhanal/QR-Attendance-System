<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Students;
use App\Models\Admin\Assignclass;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $student = Students::find(session('student_id'));

        if (!$student) {
            return redirect('/home');
        }

        $classes = Assignclass::with('subjects')
            ->where('semester', $student->current_semester)
            ->get();

        $totalSubjects = $classes
            ->pluck('subjects')
            ->flatten()
            ->count();

        return view('student.dashboard', [
            'pageTitle' => 'Dashboard',
            'student' => $student,
            'totalSubjects' => $totalSubjects
        ]);
    }
}
