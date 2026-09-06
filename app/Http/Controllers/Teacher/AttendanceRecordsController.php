<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
use App\Models\Admin\Teachers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PDF;

class AttendanceRecordsController extends Controller
{
    public function attendancerecords(Request $request)
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return redirect('/home');
        }

        // Get real Nepal date from TimeAPI
        $realDateTime = $this->getRealDateTime();

        if (! $realDateTime) {
            return redirect()->back()->with(
                'error',
                'Unable to verify the current date and time. Please check your internet connection.'
            );
        }

        $realDate = $realDateTime['date'];

        $request->validate([
            'semester' => 'nullable|integer|between:1,8',
            'subject_id' => 'nullable|exists:subjects,id',
            'status' => 'nullable|in:Present,Absent',
            'from_date' => ['nullable', 'date', 'before_or_equal:'.$realDate],
            'to_date' => [
                'nullable',
                'date',
                'before_or_equal:'.$realDate,
                'after_or_equal:from_date',
            ],
            'search' => 'nullable|string|max:100',
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
            ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('date', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('date', '<=', $request->to_date);
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
            ->orderBy('semester')
            ->orderBy('subject_id')
            ->orderByRaw("FIELD(status,'Present','Absent')")
            ->orderBy('time', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('teacher.attendancerecords', [
            'pageTitle' => 'Attendance Records',
            'teacher' => $teacher,
            'attendances' => $attendances,
            'assignedSemesters' => $assignedSemesters,
            'subjects' => $subjects,
            'realDate' => $realDate,
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return redirect('/home');
        }

        $realDateTime = $this->getRealDateTime();

        if (! $realDateTime) {
            return redirect()->back()->with(
                'error',
                'Unable to verify the current date and time. Please check your internet connection.'
            );
        }

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
            ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('date', '>=', $request->from_date);
            })

            ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('date', '<=', $request->to_date);
            })

    // Search Filter
            ->when($request->filled('search'), function ($q) use ($request) {

                $search = trim($request->search);

                $q->where(function ($query) use ($search) {

                    $query->whereHas('student', function ($student) use ($search) {
                        $student->where('name', 'like', "%{$search}%")
                            ->orWhere('roll_no', 'like', "%{$search}%")
                            ->orWhere('student_code', 'like', "%{$search}%");
                    })
                        ->orWhereHas('subject', function ($subject) use ($search) {
                            $subject->where('subject_name', 'like', "%{$search}%");
                        });

                });
            })
            ->orderByDesc('date')
            ->orderBy('semester')
            ->orderBy('subject_id')
            ->orderByRaw("FIELD(status,'Present','Absent')")
            ->orderBy('time', 'asc')
            ->get();

        $pdf = PDF::loadView(
            'teacher.attendance-pdf',
            [
                'teacher' => $teacher,
                'attendances' => $attendances,
                'realDateTime' => $realDateTime,
            ]
        );
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('teacher-attendance-report.pdf');
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
