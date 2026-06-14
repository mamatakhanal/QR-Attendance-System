<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;

class AssignclassController extends Controller
{

    public function assignclass()
    {
        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        return view('admin.assignclass', [
            'pageTitle' => 'Assign Class',
            'admin' => $admin
        ]);
    }
}