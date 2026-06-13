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
            'teacher_password' => 'required'
        ]);

        $teacher = Teachers::where('email', $request->teacher_email)->first();

        if (!$teacher || !Hash::check($request->teacher_password, $teacher->password)) {
            return back()
                ->with('error', 'Invalid credentials')
                ->with('login_type', 'teacher')
                ->withInput();
        }

        session([
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->name
        ]);

        return redirect('/teacher/dashboard');
    }

    public function studentLogin(Request $request)
    {

        $request->validate([
            'student_email' => 'required|email',
            'student_password' => 'required'
        ]);

        $student = Students::where('email', $request->student_email)->first();

        if (!$student || !Hash::check($request->student_password, $student->password)) {
            return back()
                ->with('error', 'Invalid credentials')
                ->with('login_type', 'student')
                ->withInput();
        }
        session([
            'student_id' => $student->id,
            'student_name' => $student->name

        ]);
        return redirect('/student/dashboard');
    }
}
