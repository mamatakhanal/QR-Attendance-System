<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('admin.profile', [
            'pageTitle' => 'Profile'
        ]);
    }

    // Edit Admin Profile
    public function update(Request $request, $id)
    {

        $admin = Admin::findOrFail($id);

        // Validation
        $request->validate([
            'name' => 'required|string|max:100|regex:/^[A-Za-z\s]+$/',
            'email' => [
                'required',
                'email',
                Rule::unique('admins')->ignore($id)
            ],
            'password' => 'nullable|min:8',
        ], [
            'name.regex' => 'Name must contain only letters.',
            'email.unique' => 'Email already exists.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $admin->update([
                'password' => Hash::make($request->password)
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    }
}