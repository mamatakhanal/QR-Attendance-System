<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Admin\Teachers;

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

        return view('admin.teachers', [
            'pageTitle' => 'Teachers',
            'teachers' => $teachers
        ]);
    }

    // Create Teacher
    public function create(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:100|regex:/^[A-Za-z\s]+$/',
            'email' => 'required|email|unique:teachers,email',
            'password' => 'required|min:8',
            'phone' => 'required|numeric|digits_between:7,15',
        ], [
            'name.regex' => 'Name must contain only letters.',
            'email.unique' => 'Email already exists.',
            'password.min' => 'Password must be at least 8 characters.',
            'phone.numeric' => 'Phone number must contain numbers only.',
            'phone.digits_between' => 'Phone number must be between 7 and 15 digits.',
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
            'message' => 'Teacher updated successfully'
        ]);
    }


    // Delete Student
    public function delete($id)
    {
        Teachers::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Teacher deleted successfully');
    }
}
