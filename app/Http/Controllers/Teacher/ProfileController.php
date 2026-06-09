<?php
namespace App\Http\Controllers\Teacher;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('teacher.profile', [
            'pageTitle' => 'Profile'
        ]);
    }
}