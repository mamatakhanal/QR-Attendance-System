<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function attendance()
    {
        return view('admin.attendance', [
            'pageTitle' => 'Attendance'
        ]);
    }
}