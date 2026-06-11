<?php
namespace App\Http\Controllers\Teacher;
use App\Http\Controllers\Controller;

use App\Models\Admin\Teachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('teacher.profile', [
            'pageTitle' => 'Profile'
        ]);
    }

    // Edit Profile
     public function update(Request $request, $id)
    {

        $teacher = Teachers::findOrFail($id);

        // Validation
        $request->validate([
            'name' => 'required|string|max:100|regex:/^[A-Za-z\s]+$/',
            'phone' => 'required|numeric|digits_between:7,15',
            'email' => [
                'required',
                'email',
                Rule::unique('teachers')->ignore($id)
            ],
            'password' => 'nullable|min:8',
        ], [
            'name.regex' => 'Name must contain only letters.',
            'phone.numeric' => 'Phone number must contain numbers only.',
            'phone.digits_between' => 'Phone number must be between 7 and 15 digits.',
            'email.unique' => 'Email already exists.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $teacher->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'address' => $request->address,
        ]);

        if ($request->filled('password')) {
            $teacher->update([
                'password' => Hash::make($request->password)
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    }
}