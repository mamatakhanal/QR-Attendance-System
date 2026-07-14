<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Students;
use App\Models\Admin\Teachers;
use App\Models\Admin\Subjects;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        // Students count by semester
        $studentsBySemester = Students::select(
            'current_semester',
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('current_semester')
            ->orderBy('current_semester')
            ->pluck('total', 'current_semester');

        // Prepare chart data
        $semesterLabels = [];
        $semesterData = [];

        for ($i = 1; $i <= 8; $i++) {
            $semesterLabels[] = "Semester $i";
            $semesterData[] = $studentsBySemester[$i] ?? 0;
        }

        return view('admin.dashboard', [
            'pageTitle'      => 'Dashboard',
            'admin'          => $admin,
            'studentsCount'  => Students::count(),
            'teachersCount'  => Teachers::count(),
            'subjectsCount'  => Subjects::count(),
            'semesterLabels' => $semesterLabels,
            'semesterData'   => $semesterData,
        ]);
    }
}
