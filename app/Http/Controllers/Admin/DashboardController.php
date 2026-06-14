<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;

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
            'admin' => $admin
        ]);
    }
}
