<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Students;
use App\Models\Admin\Teachers;
use App\Models\Admin\Subjects;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        // Students count by semester
        $studentsBySemester = Students::select('current_semester')
            ->selectRaw('count(*) as total')
            ->groupBy('current_semester')
            ->orderBy('current_semester')
            ->get();

        // Attendance Overview by semester
        $attendanceBySemester = \App\Models\Admin\Attendance::select('semester')
            ->selectRaw("SUM(status='Present') as present")
            ->selectRaw("SUM(status='Absent') as absent")
            ->groupBy('semester')
            ->orderBy('semester')
            ->get();

        return view('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'admin' => $admin,
            'studentsCount' => Students::count('id'),
            'teachersCount' => Teachers::count('id'),
            'subjectsCount' => Subjects::count('id'),
            'studentsBySemester' => $studentsBySemester,
                'attendanceBySemester' => $attendanceBySemester
        ]);
    }
}
