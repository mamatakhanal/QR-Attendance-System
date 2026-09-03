<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Attendance;
use App\Models\Admin\Subjects;
use App\Models\Admin\Teachers;
use Illuminate\Http\Request;
use PDF;

class AttendanceController extends Controller
{
    public function attendance(Request $request)
    {
        $admin = Admin::find(session('admin_id'));

        if (! $admin) {
            return redirect('/admin/login');
        }

         // Validate Date Range
        $request->validate([
        'from_date' => ['nullable', 'date', 'before_or_equal:today'],
        'to_date' => ['nullable', 'date', 'before_or_equal:today', 'after_or_equal:from_date'],
        ]);

        $attendances = Attendance::with([
            'teacher',
            'student',
            'subject',
        ])
            ->when($request->teacher_id, function ($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            })
            ->when($request->semester, function ($q) use ($request) {
                $q->where('semester', $request->semester);
            })
            ->when($request->subject_id, function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('date', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('date', '<=', $request->to_date);
            })
            ->when($request->search, function ($q) use ($request) {

                $q->whereHas('student', function ($student) use ($request) {

                    $student->where('name', 'like', "%{$request->search}%")
                        ->orWhere('roll_no', 'like', "%{$request->search}%")
                        ->orWhere('student_code', 'like', "%{$request->search}%");
                });
            })
            ->orderBy('date', 'desc')
            ->orderBy('semester', 'asc')
            ->orderBy('status', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.attendance', [
            'pageTitle' => 'Attendance',
            'admin' => $admin,
            'attendances' => $attendances,
            'teachers' => Teachers::orderBy('name')->get(),
            'subjects' => Subjects::orderBy('subject_name')->get(),
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $admin = Admin::find(session('admin_id'));

        if (! $admin) {
            return redirect('/admin/login');
        }

        $attendances = Attendance::with([
            'student',
            'teacher',
            'subject',
        ])

            // Semester Filter
            ->when($request->filled('semester'), function ($q) use ($request) {
                $q->where('semester', $request->semester);
            })

            // Teacher Filter
            ->when($request->filled('teacher_id'), function ($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            })

            // Student Filter
            ->when($request->filled('student_id'), function ($q) use ($request) {
                $q->where('student_id', $request->student_id);
            })

            // Status Filter
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })

            // Date Filter
            ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('date', '>=', $request->from_date);
            })

            ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('date', '<=', $request->to_date);
            })

            ->orderByDesc('date')
            ->orderBy('semester')
            ->orderBy('teacher_id')
            ->orderBy('student_id')
            ->orderByRaw("FIELD(status,'Present','Absent')")
            ->orderBy('time', 'asc')
            ->get();

        $pdf = PDF::loadView(
            'admin.attendance-pdf',
            [
                'admin' => $admin,
                'attendances' => $attendances,
                'request' => $request,
            ]
        );

        return $pdf->download('admin-attendance-report.pdf');
    }
}
