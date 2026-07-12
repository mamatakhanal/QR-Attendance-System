<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Students;
use App\Models\Admin\Attendance;
use App\Models\Admin\Subjects;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Teachers;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function attendance(Request $request)
    {
        $student = Students::find(session('student_id'));

        if (!$student) {
            return redirect('/home');
        }

        $attendances = Attendance::with(['subject', 'teacher'])
            ->where('student_id', $student->id)

            ->when($request->teacher_id, function ($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            })

            ->when($request->subject_id, function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            })

            ->when($request->date, function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            })

            ->when($request->status && $request->status != 'all', function ($q) use ($request) {
                $q->where('status', $request->status);
            })

            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(10)
            ->withQueryString();

       
        $subjects = Subjects::where('semester', $student->current_semester)
            ->orderBy('subject_name')
            ->get();

       $teachers = Teachers::whereIn(
    'id',
    Assignclass::where('semester', $student->current_semester)
        ->pluck('teacher_id')
        ->unique()
)
->orderBy('name')
->get();

        return view('student.attendance', [
            'pageTitle'   => 'Attendance',
            'student'     => $student,
            'attendances' => $attendances,
            'subjects'    => $subjects,
            'teachers'    => $teachers,
        ]);
    }
}
