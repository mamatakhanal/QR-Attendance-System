<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('admin.profile', [
            'pageTitle' => 'Profile'
        ]);
    }
}