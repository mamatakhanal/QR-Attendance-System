<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
use App\Models\Admin\Students;
use App\Models\Admin\Teachers;
use App\Services\RealTimeService;

class DashboardController extends Controller
{
    public function dashboard(RealTimeService $realTimeService)
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return redirect('/home');
        }

        // Get real Nepal date and time from external API
        $realNow = $realTimeService->now();

        if (! $realNow) {
            return back()->with(
                'error',
                'Unable to verify the real date and time. Please check your internet connection.'
            );
        }

        $realDate = $realNow->format('Y-m-d');
        $realTime = $realNow->format('H:i:s');

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

        $remaining = max(
            0,
            $totalClasses - $attendanceTaken
        );

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
}