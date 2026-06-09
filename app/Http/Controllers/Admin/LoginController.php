<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Login;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }
    public function loginCheck(Request $request)
    {

        $admin = Login::where('email', $request->email)->first();

        if (!$admin) {
            return back()->with('error', 'Invalid Email');
        }

        if (!Hash::check($request->password, $admin->password)) {
            return back()->with('error', 'Invalid Password');
        }
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        session()->forget('admin_id');
        session()->forget('admin_email');
        return redirect()->route('admin.login');
    }
}
