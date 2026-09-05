<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
use App\Models\Admin\Students;
use App\Models\Admin\Subjects;
use App\Models\Admin\Teachers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PDF;

class AttendanceController extends Controller
{
    public function attendance(Request $request)
    {
        $student = Students::find(session('student_id'));

        if (! $student) {
            return redirect('/home');
        }

        // Get real current date
        $realDateTime = $this->getRealDateTime();
        if (! $realDateTime) {
            return redirect()->back()->with(
                'error',
                'Unable to verify the current date and time. Please check your internet connection.'
            );
        }
        $realDate = $realDateTime['date'];

        $request->validate([
            'from_date' => ['nullable', 'date', 'before_or_equal:'.$realDate],
            'to_date' => [
                'nullable',
                'date',
                'before_or_equal:'.$realDate,
                'after_or_equal:from_date',
            ],
        ]);

        $attendances = Attendance::with(['subject', 'teacher'])
            ->where('student_id', $student->id)

            ->when($request->teacher_id, function ($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            })

            ->when($request->subject_id, function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            })

            ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('date', '>=', $request->from_date);
            })

            ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('date', '<=', $request->to_date);
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
            'pageTitle' => 'Attendance',
            'student' => $student,
            'attendances' => $attendances,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'realDate' => $realDate,
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $student = Students::find(session('student_id'));

        if (! $student) {
            return redirect('/home');
        }

        // Get real current date
        $realDateTime = $this->getRealDateTime();

        if (! $realDateTime) {
            return redirect()->back()->with(
                'error',
                'Unable to verify the current date and time. Please check your internet connection.'
            );
        }

        $realDate = $realDateTime['date'];

        // Validate dates using real date
        $request->validate([
            'from_date' => [
                'nullable',
                'date',
                'before_or_equal:'.$realDate,
            ],

            'to_date' => [
                'nullable',
                'date',
                'before_or_equal:'.$realDate,
                'after_or_equal:from_date',
            ],
        ]);

        $attendances = Attendance::with([
            'subject',
            'teacher',
        ])
            ->where('student_id', $student->id)

            ->when($request->filled('teacher_id'), function ($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            })

            ->when($request->filled('subject_id'), function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            })

            ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('date', '>=', $request->from_date);
            })

            ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('date', '<=', $request->to_date);
            })

            ->when($request->filled('status') && $request->status != 'all', function ($q) use ($request) {
                $q->where('status', $request->status);
            })

            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        $pdf = PDF::loadView('student.attendance-pdf', [
            'student' => $student,
            'attendances' => $attendances,
            'request' => $request,
            'realDateTime' => $realDateTime,
        ]);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('my-attendance-report.pdf');
    }

    // Get Real Date and Time
    private function getRealDateTime()
    {
        try {

            $response = Http::connectTimeout(5)
                ->timeout(5)
                ->get(
                    'https://timeapi.io/api/time/current/zone',
                    [
                        'timeZone' => 'Asia/Kathmandu',
                    ]
                );

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            if (! isset($data['date'], $data['time'])) {
                return null;
            }

            // Convert API date to YYYY-MM-DD
            $date = Carbon::parse($data['date'])->format('Y-m-d');

            // Convert API time to HH:MM:SS
            $time = Carbon::parse($data['time'])->format('H:i:s');

            return [
                'date' => $date,
                'time' => $time,
            ];

        } catch (\Throwable $e) {

            return null;
        }
    }
}
