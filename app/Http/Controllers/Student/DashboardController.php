<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Students;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $student = Students::find(session('student_id'));

        if (!$student) {
            return redirect('/home');
        }

        // Student Subjects
        $classes = Assignclass::with('subjects')
            ->where('semester', $student->current_semester)
            ->get();

        $totalSubjects = $classes
            ->pluck('subjects')
            ->flatten()
            ->count();

        $present = Attendance::where('student_id', $student->id)
            ->where('status', 'present')
            ->count();

        $absent = Attendance::where('student_id', $student->id)
            ->where('status', 'absent')
            ->count();

        $percentage = $totalSubjects > 0
            ? round(($present / $totalSubjects) * 100, 2)
            : 0;

        return view('student.dashboard', [
            'pageTitle'     => 'Dashboard',
            'student'       => $student,
            'totalSubjects' => $totalSubjects,
            'present'       => $present,
            'absent'        => $absent,
            'percentage'    => $percentage,
        ]);
    }
}