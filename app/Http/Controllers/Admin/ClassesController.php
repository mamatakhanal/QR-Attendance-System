<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;

class ClassesController extends Controller
{
    public function classes()
    {
        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        return view('admin.classes', [
            'pageTitle' => 'Classes',
            'admin' => $admin
        ]);
    }
}
