<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Teachers;
use App\Models\Admin\Attendance;
use Illuminate\Http\Request;
use App\Models\Admin\Assignclass;

class AttendanceRecordsController extends Controller
{
    public function attendancerecords(Request $request)
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (!$teacher) {
            return redirect('/home');
        }

        // Teacher assigned semesters only
        $assignedSemesters = Assignclass::where('teacher_id', $teacher->id)
            ->pluck('semester')
            ->unique()
            ->sort()
            ->values();

        $attendances = Attendance::with(['student', 'subject'])
            ->where('teacher_id', $teacher->id)

            ->when($request->semester && $request->semester != 'all', function ($q) use ($request) {
                $q->where('semester', $request->semester);
            })

            ->when($request->subject_id, function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            })

            ->when($request->date, function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            })

            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->whereHas('student', function ($student) use ($search) {
                    $student->where('name', 'like', "%{$search}%")
                        ->orWhere('roll_no', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%");
                });
            })

            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('teacher.attendancerecords', [
            'pageTitle' => 'Attendance Records',
            'teacher' => $teacher,
            'attendances' => $attendances,
            'assignedSemesters'=>$assignedSemesters,
        ]);
    }
}
