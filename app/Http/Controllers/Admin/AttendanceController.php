<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Attendance;
use App\Models\Admin\Subjects;
use App\Models\Admin\Teachers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AttendanceController extends Controller
{
    public function attendance(Request $request)
    {
        $admin = Admin::find(session('admin_id'));

        if (! $admin) {
            return redirect('/admin/login');
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

        // Validate Date Range
        $request->validate([
            'from_date' => ['nullable', 'date', 'before_or_equal:'.$realDate],
            'to_date' => [
                'nullable',
                'date',
                'before_or_equal:'.$realDate,
                'after_or_equal:from_date',
            ],
        ]);

        $attendances = Attendance::with([
            'teacher',
            'student',
            'subject',
        ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim($request->search);

                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%")
                        ->orWhere('roll_no', 'like', "%{$search}%");
                });
            })

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
            'realDate' => $realDate,
        ]);
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
