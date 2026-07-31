<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
use App\Models\Admin\AttendanceSession;
use App\Models\Admin\Students;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $student = Students::find(session('student_id'));

        if (! $student) {
            return redirect('/home');
        }

        // Student Subjects
        $classes = Assignclass::with(['subjects', 'teacher'])
            ->where('semester', $student->current_semester)
            ->get();

        $subjects = $classes->pluck('subjects')->flatten();

        $totalSubjects = $subjects->count();

        // Overall Attendance
        $present = Attendance::where('student_id', $student->id)
            ->where('status', 'present')
            ->count();

        $absent = Attendance::where('student_id', $student->id)
            ->where('status', 'absent')
            ->count();

        $totalAttendance = $present + $absent;

        $percentage = $totalAttendance > 0
            ? round(($present / $totalAttendance) * 100, 2)
            : 0;

        //  Today's Classes
        $todayClasses = [];
        foreach ($classes as $class) {
            $subject = $class->subjects->first();
            if (! $subject) {
                continue;
            }

            //  Find today's attendance session
            $session = AttendanceSession::where(
                'assign_class_id',
                $class->id
            )
                ->whereDate('date', today())
                ->latest('id')
                ->first();

            // Determine Attendance Status
            if (! $session) {
                $status = 'Not Taken';
                $statusClass = 'secondary';
                $statusIcon = 'bi-dash-circle';
            } elseif ($session->status === 'Open') {
                $status = 'Open';
                $statusClass = 'warning';
                $statusIcon = 'bi-hourglass-split';
            } else {
                $status = 'Taken';
                $statusClass = 'success';
                $statusIcon = 'bi-check-circle-fill';
            }

            // Teacher
            $teacherName = $class->teacher->name ?? '-';

            //  Time
            $time = $session
        ? Carbon::parse($session->start_time)->format('h:i A')
        : '-';

            // Store Today Class
            $todayClasses[] = [
                'subject' => $subject->subject_name,
                'teacher' => $teacherName,
                'time' => $time,
                'status' => $status,
                'statusClass' => $statusClass,
                'statusIcon' => $statusIcon,
            ];
        }

        // Chart + Subject Status
        $subjectLabels = [];
        $subjectPercentages = [];
        $subjectStatuses = [];

        foreach ($subjects as $subject) {

            $total = Attendance::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->count();

            $presentCount = Attendance::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->where('status', 'present')
                ->count();

            $subjectPercentage = $total > 0
                ? round(($presentCount / $total) * 100)
                : 0;

            // Chart Data
            $subjectLabels[] = $subject->subject_name;
            $subjectPercentages[] = $subjectPercentage;

            // Subject Status
            if ($subjectPercentage >= 90) {
                $subjectStatuses[] = [
                    'subject' => $subject->subject_name,
                    'class' => 'success',
                    'icon' => 'bi-check-circle-fill',
                    'title' => 'Excellent',
                    'message' => 'Excellent attendance. Keep it up!',
                    'percentage' => $subjectPercentage,
                ];
            } elseif ($subjectPercentage >= 75) {
                $subjectStatuses[] = [
                    'subject' => $subject->subject_name,
                    'class' => 'primary',
                    'icon' => 'bi-hand-thumbs-up-fill',
                    'title' => 'Good',
                    'message' => 'Good attendance. Maintain it.',
                    'percentage' => $subjectPercentage,
                ];
            } else {
                $subjectStatuses[] = [
                    'subject' => $subject->subject_name,
                    'class' => 'danger',
                    'icon' => 'bi-exclamation-triangle-fill',
                    'title' => 'Warning',
                    'message' => 'Attendance below 75%. Please improve.',
                    'percentage' => $subjectPercentage,
                ];
            }
        }

        return view('student.dashboard', [
            'pageTitle' => 'Dashboard',
            'student' => $student,
            'totalSubjects' => $totalSubjects,
            'present' => $present,
            'absent' => $absent,
            'percentage' => $percentage,
            'todayClasses' => $todayClasses,

            // Chart
            'subjectLabels' => $subjectLabels,
            'subjectPercentages' => $subjectPercentages,

            // Subject Status
            'subjectStatuses' => $subjectStatuses,
        ]);
    }
}
