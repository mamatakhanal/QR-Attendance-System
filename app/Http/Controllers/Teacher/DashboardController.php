<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
use App\Models\Admin\AttendanceSession;
use App\Models\Admin\Students;
use App\Models\Admin\Teachers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return redirect('/home');
        }

        // Get real Nepal date and time
        $realDateTime = $this->getRealDateTime();

        if (! $realDateTime) {
            return back()->with(
                'error',
                'Unable to verify the current date and time. Please check your internet connection.'
            );
        }

        $realDate = $realDateTime['date'];
        $realTime = $realDateTime['time'];

        $assignclasses = Assignclass::with('subjects')
            ->where('teacher_id', $teacher->id)
            ->get();

        $totalClasses = $assignclasses->count();

        $totalSubjects = $assignclasses
            ->pluck('subjects')
            ->flatten()
            ->count();

        $totalStudents = Students::whereIn(
            'current_semester',
            $assignclasses->pluck('semester')
        )->count();

        $attendanceTaken = Attendance::where('teacher_id', $teacher->id)
            ->where('date', $realDate)
            ->distinct()
            ->count('subject_id');

        $remaining = max(0, $totalClasses - $attendanceTaken);

        return view('teacher.dashboard', [
            'pageTitle' => 'Dashboard',
            'teacher' => $teacher,
            'totalClasses' => $totalClasses,
            'totalSubjects' => $totalSubjects,
            'totalStudents' => $totalStudents,

            'attendanceTaken' => $attendanceTaken,
            'remaining' => $remaining,

            'realDate' => $realDate,
            'realTime' => $realTime,
        ]);
    }

    private function getRealDateTime()
    {
        try {

            $response = Http::connectTimeout(3)
                ->timeout(5)
                ->get(
                    'https://timeapi.io/api/time/current/zone',
                    [
                        'timeZone' => 'Asia/Kathmandu',
                    ]
                );

            if ($response->successful()) {

                $data = $response->json();

                if (
                    isset($data['date']) &&
                    isset($data['time'])
                ) {

                    return [
                        'date' => Carbon::parse(
                            $data['date']
                        )->format('Y-m-d'),

                        'time' => Carbon::parse(
                            $data['time']
                        )->format('H:i:s'),
                    ];
                }
            }

        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }
}
