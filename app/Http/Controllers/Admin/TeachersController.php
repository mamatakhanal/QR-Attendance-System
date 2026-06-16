<?php

namespace App\Http\Controllers\Admin;

use App\Mail\TeacherMail;
use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Teachers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class TeachersController extends Controller
{

    public function teachers(Request $request)
    {
        $search = $request->search;

        $teachers = Teachers::when($search, function ($query) use ($search) {
            $query->Where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->paginate(10)
            ->withQueryString();

        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        return view('admin.teachers', [
            'pageTitle' => 'Teachers',
            'teachers' => $teachers,
            'admin' => $admin
        ]);
    }

    // Create Teacher
    public function create(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:100|regex:/^[A-Za-z\s]+$/',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'required|numeric|regex:/^[9][0-9]{9}$/',
            'password' => 'required|min:8',
        ], [
            'name.regex' => 'Name must contain only letters.',
            'email.unique' => 'Email already exists.',
            'phone.numeric' => 'Phone number must contain numbers only.',
            'phone.regex' => 'Phone number must start with 9 and be exactly 10 digits.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $teacher = Teachers::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Teacher added successfully'
        ]);
    }

    // Edit Teacher
    public function update(Request $request, $id)
    {

        $teacher = Teachers::findOrFail($id);

        // Validation
        $request->validate([
            'name' => 'required|string|max:100|regex:/^[A-Za-z\s]+$/',
            'phone' => 'required|numeric|regex:/^[9][0-9]{9}$/',
            'email' => [
                'required',
                'email',
                Rule::unique('teachers')->ignore($id)
            ],
            'password' => 'nullable|min:8',
        ], [
            'name.regex' => 'Name must contain only letters.',
            'phone.numeric' => 'Phone number must contain numbers only.',
            'phone.regex' => 'Phone number must start with 9 and be exactly 10 digits.',
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
            'message' => 'Teacher updated successfully'
        ]);
    }


    // Delete Student
    public function delete($id)
    {
        Teachers::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Teacher deleted successfully');
    }

    // Send Mail
    public function sendEmail($id)
    {
        try {
            $teacher = Teachers::findOrFail($id);

            $plainPassword = Str::random(8);
            $teacher->update([
                'password' => Hash::make($plainPassword)
            ]);

            Mail::to($teacher->email)
                ->queue(new TeacherMail($teacher, $plainPassword));

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
