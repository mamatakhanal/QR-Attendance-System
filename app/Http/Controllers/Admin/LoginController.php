<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function loginCheck(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return back()->with('error', 'Invalid email or password');
        }

        if (!Hash::check($request->password, $admin->password)) {
            return back()->with('error', 'Invalid email or password');
        }
        // LOGIN SUCCESS - store session
        session([
            'admin_id' => $admin->id,
            'admin_email' => $admin->email
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        session()->forget('admin_id');
        session()->forget('admin_email');
        return redirect()->route('admin.login');
    }
}
