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
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        // AJAX request
        if ($request->ajax()) {
            return view('admin.teachers', [
                'pageTitle' => 'Teachers',
                'teachers' => $teachers
            ])->render();
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

        Teachers::create([
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
        $teacher = Teachers::findOrFail($id);

        // Check if teacher has assigned classes
        if ($teacher->assignClasses()->exists()) {
            return redirect()->back()->with(
                'error',
                'Assigned teacher cannot be deleted.'
            );
        }

        $teacher->delete();
        return redirect()->back()->with(
            'success',
            'Teacher deleted successfully.'
        );
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
                ->send(new TeacherMail($teacher, $plainPassword));

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
