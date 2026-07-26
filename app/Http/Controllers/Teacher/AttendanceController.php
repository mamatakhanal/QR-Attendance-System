<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
use App\Models\Admin\AttendanceSession;
use App\Models\Admin\Students;
use App\Models\Admin\Teachers;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function attendance()
    {
        $teacher = Teachers::find(session('teacher_id'));
        if (! $teacher) {
            return redirect('/home');
        }

        $assignclasses = Assignclass::with('subjects')
            ->where('teacher_id', $teacher->id)
            ->orderBy('semester')
            ->get();

        foreach ($assignclasses as $assignclass) {
            $assignclass->student_count = Students::where(
                'current_semester',
                $assignclass->semester
            )->count();
        }

        return view('teacher.attendance', [
            'pageTitle' => 'Attendance',
            'teacher' => $teacher,
            'assignclasses' => $assignclasses,
        ]);
    }


    // Scan Attendance
    public function scanAttendance(Request $request)
    {
        // Logged in teacher
        $teacher = Teachers::find(session('teacher_id'));
        if (! $teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Your session has expired. Please log in again.',
            ]);
        }

        // Selected class
        $assignClass = Assignclass::with('subjects')->find($request->assign_class_id);
        if (! $assignClass) {
            return response()->json([
                'success' => false,
                'message' => 'The selected class could not be found. Please select a class and try again.',
            ]);
        }

        $session = AttendanceSession::where('assign_class_id', $assignClass->id)
            ->where('teacher_id', $teacher->id)
            ->whereDate('date', today())
            ->where('status', 'Open')
            ->first();

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance session has not been started.',
            ]);
        }

        // Session time expired
        if (now()->greaterThanOrEqualTo($session->end_time)) {

            // Mark remaining students absent
            $this->markAbsentStudents($assignClass);

            // Close the session
            $session->update([
                'status' => 'Closed',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'The Attendance period has ended. <br><br>Students who did not scan their QR code within the session have been marked <strong>Absent</strong>.',
            ]);
        }

        // Decode QR
        $qr = json_decode($request->qr_data, true);
        if (
            ! $qr ||
            ! isset($qr['student_id']) ||
            ! isset($qr['student_code'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'The scanned QR code does not belong to any student in the system. <br> Please scan a student\'s attendance QR code.',
            ]);
        }

        // Find student
        $student = Students::find($qr['student_id']);
        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'The scanned student could not be found.',
            ]);
        }

        // Check student belongs to selected semester
        if ($student->current_semester != $assignClass->semester) {
            return response()->json([
                'success' => false,
                'message' => 'This student does not belong to the selected class.',
            ]);
        }

        // Check if attendance marked for today
        $attendance = Attendance::where('student_id', $student->id)
            ->where('teacher_id', $teacher->id)
            ->where('subject_id', $assignClass->subjects->first()->id)
            ->whereDate('date', today())
            ->first();

        if ($attendance) {

            if ($attendance->status == 'Present') {
                return response()->json([
                    'success' => false,
                    'message' => 'This student\'s attendance has already been marked for today.',
                ]);
            }

            if ($attendance->status == 'Absent') {
                return response()->json([
                    'success' => false,
                    'message' => 'The Attendance period has ended. <br> <br> Students who did not scan their QR code within the session have been marked <strong> Absent</strong>.',
                ]);
            }
        }

        Attendance::create([
            'semester' => $student->current_semester,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $assignClass->subjects->first()->id,
            'date' => now()->toDateString(),
            'time' => now()->format('H:i:s'),
            'status' => 'Present',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance Marked Successfully',

            'student' => [
                'name' => $student->name,
                'student_code' => $student->student_code,
                'current_semester' => $student->current_semester,
            ],

            'subject' => $assignClass->subjects->first()->subject_name,

            'date' => now()->format('d M Y'),

            'time' => now()->format('h:i A'),
        ]);
    }


    // Start Attendance Session
    public function startSession(Request $request)
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return response()->json([
                'success' => false,
            ]);
        }

        $assignClass = Assignclass::with('subjects')
            ->find($request->assign_class_id);

        if (! $assignClass) {
            return response()->json([
                'success' => false,
            ]);
        }

        $subject = $assignClass->subjects->first();

        if (! $subject) {
            return response()->json([
                'success' => false,
                'message' => 'No subject is assigned to this class.',
            ]);
        }

        // Check if today's session already exists
        $session = AttendanceSession::where('assign_class_id', $assignClass->id)
            ->where('teacher_id', $teacher->id)
            ->whereDate('date', today())
            ->first();

        // Check if today's session already open
        if ($session) {

            // Session expired but still marked Open
            if ($session->status == 'Open' && now()->greaterThanOrEqualTo($session->end_time)) {

                $this->markAbsentStudents($assignClass);
                $session->update([
                    'status' => 'Closed',
                ]);
            }

            // Already closed today
            if ($session->status == 'Closed') {
                return response()->json([
                    'success' => true,
                    'type' => 'closed',
                    'message' => 'Attendance for this class has already ended.<br> All students who did not scan their QR code have been marked as <strong>Absent</strong>.',
                ]);
            }

            // Session is still active
            return response()->json([
                'success' => true,
                'type' => 'open',
            ]);
        }

        return response()->json([
            'success' => true,
            'type' => 'new',
        ]);
    }


    // Create Attendance Session
    public function createSession(Request $request)
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return response()->json([
                'success' => false,
            ]);
        }

        $assignClass = Assignclass::with('subjects')
            ->find($request->assign_class_id);

        if (! $assignClass) {
            return response()->json([
                'success' => false,
            ]);
        }

        $subject = $assignClass->subjects->first();

        // Prevent duplicate session
        $exists = AttendanceSession::where('assign_class_id', $assignClass->id)
            ->where('teacher_id', $teacher->id)
            ->whereDate('date', today())
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
            ]);
        }

        AttendanceSession::create([
            'assign_class_id' => $assignClass->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'date' => today(),
            'start_time' => now(),
            'end_time' => now()->addMinutes(40),
            // 'end_time' => now()->addMinutes(60),
            // 'end_time' => now()->addHour(2),
            'status' => 'Open',
        ]);

        return response()->json([
            'success' => true,
             'type' => 'new',
        ]);
    }


    // Mark Absent Students
    public function markAbsentStudents($assignClass)
    {
        $students = Students::where(
            'current_semester',
            $assignClass->semester
        )->get();

        foreach ($students as $student) {

            $exists = Attendance::where('student_id', $student->id)
                ->where('teacher_id', session('teacher_id'))
                ->where('subject_id', $assignClass->subjects->first()->id)
                ->whereDate('date', today())
                ->exists();

            if (! $exists) {

                Attendance::create([
                    'semester' => $student->current_semester,
                    'student_id' => $student->id,
                    'teacher_id' => session('teacher_id'),
                    'subject_id' => $assignClass->subjects->first()->id,
                    'date' => today(),
                    'time' => now()->format('H:i:s'),
                    'status' => 'Absent',
                ]);

            }
        }
    }


    // Get Attendance Count
    public function getAttendanceCount(Request $request)
    {
        $teacher = Teachers::find(session('teacher_id'));

        $assignClass = Assignclass::with('subjects')
            ->find($request->assign_class_id);

        $subject = $assignClass->subjects->first();

        $present = Attendance::where('teacher_id', $teacher->id)
            ->where('subject_id', $subject->id)
            ->whereDate('date', today())
            ->where('status', 'Present')
            ->count();

        $absent = Attendance::where('teacher_id', $teacher->id)
            ->where('subject_id', $subject->id)
            ->whereDate('date', today())
            ->where('status', 'Absent')
            ->count();

        return response()->json([
            'present' => $present,
            'absent' => $absent,
            'total' => Students::where('current_semester', $assignClass->semester)->count(),
        ]);
    }
}
