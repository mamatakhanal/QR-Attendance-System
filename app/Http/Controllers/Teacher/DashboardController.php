<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Teachers;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Students;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (!$teacher) {
            return redirect('/home');
        }

        $assignclasses = Assignclass::with('subjects')
            ->where('teacher_id', $teacher->id)
            ->get();

        $totalClasses = $assignclasses->count();

        $totalSubjects = $assignclasses
            ->pluck('subjects')
            ->flatten()
            ->count();

        $totalStudents = Students::whereIn(
            'current_semester',
            $assignclasses->pluck('semester')
        )->count();

        return view('teacher.dashboard', [
            'pageTitle' => 'Dashboard',
            'teacher' => $teacher,
            'totalClasses' => $totalClasses,
            'totalSubjects' => $totalSubjects,
            'totalStudents' => $totalStudents
        ]);
    }
}
