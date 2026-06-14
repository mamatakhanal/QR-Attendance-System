<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;

class SubjectsController extends Controller
{
    public function subjects()
    {
        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        return view('admin.subjects', [
            'pageTitle' => 'Subjects',
            'admin' => $admin
        ]);
    }
}
