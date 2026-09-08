<?php

namespace App\Http\Controllers\Mainpage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\Teachers;
use App\Models\Admin\Students;

class LoginController extends Controller
{

    public function teacherLogin(Request $request)
    {
        $request->validate([
            'teacher_email' => 'required|email',
            'teacher_password' => 'required',
        ]);

        $teacher = Teachers::where('email', $request->teacher_email)->first();

        if (!$teacher) {
            return redirect()->route('mainpage.home')
                ->with('error', 'Teacher email not found.')
                ->with('login_type', 'teacher');
        }

        if (!Hash::check($request->teacher_password, $teacher->password)) {
            return redirect()->route('mainpage.home')
                ->with('error', 'Incorrect teacher password.')
                ->with('login_type', 'teacher');
        }

        // Regenerate session after successful login
        $request->session()->regenerate();

        // Store teacher information in session
        $request->session()->put([
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->name,
        ]);

        return redirect()->route('teacher.dashboard');
    }


    public function studentLogin(Request $request)
    {
        $request->validate([
            'student_email' => 'required|email',
            'student_password' => 'required',
        ]);

        $student = Students::where('email', $request->student_email)->first();

        if (!$student) {
            return redirect()->route('mainpage.home')
                ->with('error', 'Student email not found.')
                ->with('login_type', 'student');
        }

        if (!Hash::check($request->student_password, $student->password)) {
            return redirect()->route('mainpage.home')
                ->with('error', 'Incorrect student password.')
                ->with('login_type', 'student');
        }

        // Regenerate session after successful login
        $request->session()->regenerate();

        // Store student information in session
        $request->session()->put([
            'student_id' => $student->id,
            'student_name' => $student->name,
        ]);

        return redirect()->route('student.dashboard');
    }
}