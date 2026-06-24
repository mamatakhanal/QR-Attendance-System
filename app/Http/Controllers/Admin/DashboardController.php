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

        return view('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'admin' => $admin,
            'studentsCount' => Students::count('id'),
            'teachersCount' => Teachers::count('id'),
            'subjectsCount' => Subjects::count('id')
        ]);
    }
}
