<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;

class AttendanceController extends Controller
{
    public function attendance()
    {
        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        return view('admin.attendance', [
            'pageTitle' => 'Attendance',
            'admin' => $admin
        ]);
    }
}
