<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('student.dashboard', [
            'pageTitle' => 'Dashboard'
        ]);
    }
}
