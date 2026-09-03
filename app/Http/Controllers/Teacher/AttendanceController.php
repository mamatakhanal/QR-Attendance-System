<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
use App\Models\Admin\AttendanceSession;
use App\Models\Admin\Students;
use App\Models\Admin\Teachers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AttendanceController extends Controller
{
    public function attendance(Request $request)
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

        $realDate = $realDateTime['date'];
        $realTime = $realDateTime['time'];

        $currentDateTime = Carbon::parse(
            $realDate.' '.$realTime,
            'Asia/Kathmandu'
        );

        $oldSessions = AttendanceSession::where('teacher_id', $teacher->id)
            ->where('status', 'Open')
            ->get();

        foreach ($oldSessions as $oldSession) {

            $sessionEnd = Carbon::parse(
                $oldSession->date.' '.$oldSession->end_time,
                'Asia/Kathmandu'
            );

            if ($currentDateTime->greaterThanOrEqualTo($sessionEnd)) {

                $assignClass = Assignclass::with('subjects')
                    ->find($oldSession->assign_class_id);

                if ($assignClass) {
                    $this->markAbsentStudents(
                        $assignClass,
                        $oldSession->teacher_id,
                        $oldSession->date
                    );
                }

                $oldSession->update([
                    'status' => 'Closed',
                ]);
            }
        }

        // Get classes assigned to this teacher
        $assignclasses = Assignclass::with('subjects')
            ->where('teacher_id', $teacher->id)
            ->orderBy('semester')
            ->get();

        // Count students in each semester
        foreach ($assignclasses as $assignclass) {

            $assignclass->student_count = Students::where(
                'current_semester',
                $assignclass->semester
            )->count();
        }

        // Selected class
        $selectedClass = $request->assign_class_id;

        $currentClass = null;

        if ($selectedClass) {

            $currentClass = Assignclass::with([
                'subjects',
                'teacher',
            ])->find($selectedClass);

            if ($currentClass) {

                $currentClass->student_count = Students::where(
                    'current_semester',
                    $currentClass->semester
                )->count();
            }
        }

        return view('teacher.attendance', [
            'pageTitle' => 'Attendance',
            'teacher' => $teacher,
            'assignclasses' => $assignclasses,
            'selectedClass' => $selectedClass,
            'currentClass' => $currentClass,
        ]);
    }

    // Scan Attendance
    public function scanAttendance(Request $request)
    {
        $realDateTime = $this->getRealDateTime();

        if (! $realDateTime) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify the current date and time. Please check your internet connection and try again.',
            ]);
        }

        $realDate = $realDateTime['date'];
        $realTime = $realDateTime['time'];

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
            ->whereDate('date', $realDate)
            ->where('status', 'Open')
            ->first();

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance session has not been started.',
            ]);
        }

        // Session time expired
        $currentTime = Carbon::parse(
            $realDate.' '.$realTime,
            'Asia/Kathmandu'
        );

        $sessionEnd = Carbon::parse(
            $session->date.' '.$session->end_time,
            'Asia/Kathmandu'
        );

        if ($currentTime->greaterThanOrEqualTo($sessionEnd)) {

            // Mark remaining students absent
            $this->markAbsentStudents(
                $assignClass,
                $teacher->id,
                $session->date
            );
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
            ->whereDate('date', $realDate)
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
            'assign_class_id' => $assignClass->id,
            'date' => $realDate,
            'time' => $realTime,
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
            'date' => Carbon::parse($realDate)->format('d M Y'),
            'time' => Carbon::parse($realTime)->format('h:i A'),
        ]);
    }

    // Start Attendance Session
    public function startSession(Request $request)
    {
        $realDateTime = $this->getRealDateTime();

        if (! $realDateTime) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify the current date and time. Please check your internet connection and try again.',
            ]);
        }

        $realDate = $realDateTime['date'];
        $realTime = $realDateTime['time'];

        $currentTime = Carbon::parse(
            $realDate.' '.$realTime,
            'Asia/Kathmandu'
        );

        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return response()->json([
                'success' => false,
            ]);
        }

        $assignClass = Assignclass::with('subjects')
            ->where('id', $request->assign_class_id)
            ->where('teacher_id', $teacher->id)
            ->first();

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
            ->whereDate('date', $realDate)
            ->first();

        // Check if today's session already open
        if ($session) {

            if ($session->status == 'Open') {

                $sessionEnd = Carbon::parse(
                    $session->date.' '.$session->end_time,
                    'Asia/Kathmandu'
                );

                if ($currentTime->greaterThanOrEqualTo($sessionEnd)) {

                    $this->markAbsentStudents(
                        $assignClass,
                        $teacher->id,
                        $session->date
                    );

                    $session->update([
                        'status' => 'Closed',
                    ]);

                    return response()->json([
                        'success' => true,
                        'type' => 'closed',
                        'message' => 'Attendance session has expired. Remaining students have been marked Absent.',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'type' => 'open',
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
        $realDateTime = $this->getRealDateTime();

        if (! $realDateTime) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify the current date and time. Please check your internet connection and try again.',
            ]);
        }

        $realDate = $realDateTime['date'];
        $realTime = $realDateTime['time'];

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
            ->whereDate('date', $realDate)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
            ]);
        }

        $startTime = Carbon::parse(
            $realDate.' '.$realTime,
            'Asia/Kathmandu'
        );

        $endTime = $startTime->copy()->addMinutes(2);
        // $endTime = $startTime->copy()->addHours(2);

        AttendanceSession::create([
            'assign_class_id' => $assignClass->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'date' => $realDate,
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
            'status' => 'Open',
        ]);

        return response()->json([
            'success' => true,
            'type' => 'new',
        ]);
    }

    // Mark Absent Students
    public function markAbsentStudents($assignClass, $teacherId, $date)
    {
        // Get the first subject assigned to this class
        $subject = $assignClass->subjects->first();

        if (! $subject) {
            return;
        }

        // Get students from the assigned semester
        $students = Students::where(
            'current_semester',
            $assignClass->semester
        )->get();

        foreach ($students as $student) {

            // Check if attendance already exists
            $exists = Attendance::where('student_id', $student->id)
                ->where('teacher_id', $teacherId)
                ->where('subject_id', $subject->id)
                ->whereDate('date', $date)
                ->exists();

            // If attendance does not exist, mark student as Absent
            if (! $exists) {

                Attendance::create([
                    'semester' => $student->current_semester,
                    'student_id' => $student->id,
                    'teacher_id' => $teacherId,
                    'subject_id' => $subject->id,
                    'date' => $date,
                    'time' => null,
                    'status' => 'Absent',
                ]);
            }
        }
    }

    // Get Attendance Count
    public function getAttendanceCount(Request $request)
    {
        $realDateTime = $this->getRealDateTime();

        if (! $realDateTime) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to get the current real date and time.',
            ]);
        }

        $realDate = $realDateTime['date'];

        $teacher = Teachers::find(session('teacher_id'));

        $assignClass = Assignclass::with('subjects')
            ->find($request->assign_class_id);

        $subject = $assignClass->subjects->first();

        $present = Attendance::where('teacher_id', $teacher->id)
            ->where('subject_id', $subject->id)
            ->whereDate('date', $realDate)
            ->where('status', 'Present')
            ->count();

        $absent = Attendance::where('teacher_id', $teacher->id)
            ->where('subject_id', $subject->id)
            ->whereDate('date', $realDate)
            ->where('status', 'Absent')
            ->count();

        return response()->json([
            'present' => $present,
            'absent' => $absent,
            'total' => Students::where('current_semester', $assignClass->semester)->count(),
        ]);
    }

    // Real time
    private function getRealDateTime()
    {
        try {

            $response = Http::connectTimeout(2)
                ->timeout(4)
                ->get(
                    'https://timeapi.io/api/time/current/zone',
                    [
                        'timeZone' => 'Asia/Kathmandu',
                    ]
                );

            if ($response->successful()) {

                $data = $response->json();

                if (isset($data['date'], $data['time'])) {

                    $date = Carbon::parse($data['date'])
                        ->format('Y-m-d');

                    $time = Carbon::parse($data['time'])
                        ->format('H:i:s');

                    return [
                        'date' => $date,
                        'time' => $time,
                    ];
                }
            }

        } catch (\Throwable $e) {

            // Never use local computer time.
            return null;
        }

        return null;
    }
}
