<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Teachers;
use App\Models\Admin\Attendance;
use App\Models\Admin\Assignclass;
use Illuminate\Http\Request;

class AttendanceRecordsController extends Controller
{
    public function attendancerecords(Request $request)
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (!$teacher) {
            return redirect('/home');
        }

        $request->validate([
            'semester'  => 'nullable|integer|between:1,8',
            'subject_id' => 'nullable|exists:subjects,id',
            'status'    => 'nullable|in:present,absent',
            'date'      => 'nullable|date',
            'search'    => 'nullable|string|max:100',
        ]);

        // Teacher assigned semesters
        $assignedSemesters = Assignclass::where('teacher_id', $teacher->id)
            ->pluck('semester')
            ->unique()
            ->sort()
            ->values();

        // Teacher assigned subjects
        $subjects = Assignclass::where('teacher_id', $teacher->id)
            ->with('subjects')
            ->get()
            ->pluck('subjects')
            ->flatten()
            ->unique('id')
            ->values();

        // Attendance Records
        $attendances = Attendance::with(['student', 'subject'])
            ->where('teacher_id', $teacher->id)

            // Semester Filter
            ->when($request->filled('semester'), function ($q) use ($request) {
                $q->where('semester', $request->semester);
            })

            // Subject Filter
            ->when($request->filled('subject_id'), function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            })

            // Status Filter
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })

            // Date Filter
            ->when($request->filled('date'), function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            })

            // Search
            ->when($request->filled('search'), function ($q) use ($request) {

                $search = trim($request->search);

                $q->where(function ($query) use ($search) {

                    // Student Search
                    $query->whereHas('student', function ($student) use ($search) {
                        $student->where('name', 'like', "%{$search}%")
                            ->orWhere('roll_no', 'like', "%{$search}%")
                            ->orWhere('student_code', 'like', "%{$search}%");
                    })

                        // Subject Search
                        ->orWhereHas('subject', function ($subject) use ($search) {
                            $subject->where('subject_name', 'like', "%{$search}%");
                        });
                });
            })

            ->orderByDesc('date')
            ->orderByDesc('time')
            ->paginate(10)
            ->withQueryString();

        return view('teacher.attendancerecords', [
            'pageTitle'          => 'Attendance Records',
            'teacher'            => $teacher,
            'attendances'        => $attendances,
            'assignedSemesters'  => $assignedSemesters,
            'subjects'           => $subjects,
        ]);
    }
}
