<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('student.profile', [
            'pageTitle' => 'Profile'
        ]);
    }
}