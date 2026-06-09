<?php

namespace App\Http\Controllers\Mainpage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\Teachers;
use App\Models\Admin\Students;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        if ($request->role == 'teacher') {

            $teacher = Teachers::where('email', $request->email)->first();

            if (!$teacher || !Hash::check($request->password, $teacher->password)) {
                return back()->with('error', 'Invalid email or password');
            }

            session([
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->name
            ]);

            return redirect('/teacher/dashboard');
        }

        if ($request->role == 'student') {

            $student = Students::where('email', $request->email)->first();

            if (!$student || !Hash::check($request->password, $student->password)) {
                return back()->with('error', 'Invalid email or password');
            }

            session([
                'student_id' => $student->id,
                'student_name' => $student->name
            ]);

            return redirect('/student/dashboard');
        }

        return back()->with('error', 'Invalid login type');
    }
}