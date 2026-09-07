<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
use App\Models\Admin\AttendanceSession;
use App\Models\Admin\ClassReplacement;
use App\Models\Admin\Students;
use App\Models\Admin\Teachers;
use App\Services\RealTimeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function attendance(Request $request, RealTimeService $realTimeService)
    {

        $teacher = Teachers::find(session('teacher_id'));
        if (! $teacher) {
            return redirect('/home');
        }

        $realNow = $realTimeService->now();

        if (! $realNow) {
            return redirect()->back()->with(
                'error',
                'Unable to verify the current date and time. Please check your internet connection.'
            );
        }

        $realDate = $realNow->format('Y-m-d');
        $realTime = $realNow->format('H:i:s');

        $currentDateTime = $realNow->copy()->setTimezone('Asia/Kathmandu');

        $oldSessions = AttendanceSession::where('teacher_id', $teacher->id)
            ->where('status', 'Open')
            ->get();

        foreach ($oldSessions as $oldSession) {

            $sessionEnd = Carbon::parse($oldSession->date)
                ->setTimeFromTimeString(
                    Carbon::parse($oldSession->end_time)->format('H:i:s')
                )
                ->setTimezone('Asia/Kathmandu');

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

        // Get permanent classes assigned to this teacher
        $assignclasses = Assignclass::with('subjects')
            ->where('teacher_id', $teacher->id)
            ->orderBy('semester')
            ->get();

        // Count students
        foreach ($assignclasses as $assignclass) {

            $assignclass->student_count = Students::where(
                'current_semester',
                $assignclass->semester
            )->count();

            $assignclass->is_replacement = false;
        }

        // Get today's replacement classes for this teacher
        $replacements = ClassReplacement::with([
            'subject',
            'assignclass',
        ])
            ->where(
                'replacement_teacher_id',
                $teacher->id
            )
            ->whereDate(
                'date',
                $realDate
            )
            ->orderBy('start_time')
            ->get();

        foreach ($replacements as $replacement) {

            $replacement->student_count = Students::where(
                'current_semester',
                $replacement->subject->semester
            )->count();

            $replacement->is_replacement = true;
        }

        // Selected class
        $selectedClass = $request->assign_class_id;

        $currentClass = null;

        if ($selectedClass) {

            $currentClass = Assignclass::with([
                'subjects',
                'teacher',
            ])
                ->where('id', $selectedClass)
                ->where('teacher_id', $teacher->id)
                ->first();

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
            'replacements' => $replacements,
        ]);
    }

    // Scan Attendance
    public function scanAttendance(Request $request, RealTimeService $realTimeService)
    {
        $realNow = $realTimeService->now();

        if (! $realNow) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify the current date and time. Please check your internet connection and try again.',
            ]);
        }

        $realDate = $realNow->format('Y-m-d');
        $realTime = $realNow->format('H:i:s');

        // Logged in teacher
        $teacher = Teachers::find(session('teacher_id'));
        if (! $teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Your session has expired. Please log in again.',
            ]);
        }

        // Selected class
        $assignClass = Assignclass::with('subjects')
            ->where('id', $request->assign_class_id)
            ->first();

        if (! $assignClass) {
            return response()->json([
                'success' => false,
                'message' => 'The selected class could not be found. Please select a class and try again.',
            ]);
        }

        $subject = $assignClass->subjects->first();

        $replacement = ClassReplacement::where(
            'assign_class_id',
            $assignClass->id
        )
            ->where(
                'replacement_teacher_id',
                $teacher->id
            )
            ->whereDate(
                'date',
                $realDate
            )
            ->first();

        if (! $subject) {
            return response()->json([
                'success' => false,
                'message' => 'No subject is assigned to this class.',
            ]);
        }

        $sessionQuery = AttendanceSession::where(
            'assign_class_id',
            $assignClass->id
        )
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->whereDate(
                'date',
                $realDate
            )
            ->where(
                'status',
                'Open'
            );

        if ($replacement) {

            $sessionQuery->where(
                'replacement_id',
                $replacement->id
            );

        } else {

            $sessionQuery->whereNull(
                'replacement_id'
            );
        }

        $session = $sessionQuery->first();

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance session has not been started.',
            ]);
        }

        // Session time expired
        $currentTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $realDate.' '.$realTime,
            'Asia/Kathmandu'
        );

        $sessionEnd = Carbon::parse($session->date)
            ->setTimeFromTimeString(
                Carbon::parse($session->end_time)->format('H:i:s')
            )
            ->setTimezone('Asia/Kathmandu');

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

        $attendanceQuery = Attendance::where(
            'student_id',
            $student->id
        )
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->where(
                'subject_id',
                $assignClass->subjects->first()->id
            )
            ->where(
                'assign_class_id',
                $assignClass->id
            )
            ->whereDate(
                'date',
                $realDate
            );

        if ($replacement) {

            $attendanceQuery->where(
                'replacement_id',
                $replacement->id
            );

        } else {

            $attendanceQuery->whereNull(
                'replacement_id'
            );
        }

        $attendance = $attendanceQuery->first();

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

        $realDateTimeValue = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $realDate.' '.$realTime,
            'Asia/Kathmandu'
        );

        Attendance::create([
            'semester' => $student->current_semester,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $assignClass->subjects->first()->id,
            'assign_class_id' => $assignClass->id,
            'replacement_id' => $replacement?->id,
            'date' => $realDate,
            'time' => $realTime,
            'status' => 'Present',

            'created_at' => $realDateTimeValue,
            'updated_at' => $realDateTimeValue,
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
    public function startSession(Request $request, RealTimeService $realTimeService)
    {
        $realNow = $realTimeService->now();

        if (! $realNow) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify the current date and time. Please check your internet connection and try again.',
            ]);
        }

        $realDate = $realNow->format('Y-m-d');
        $realTime = $realNow->format('H:i:s');

        $currentTime = $realNow->copy()->setTimezone('Asia/Kathmandu');

        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found.',
            ]);
        }

        $assignClass = Assignclass::with('subjects')
            ->where('id', $request->assign_class_id)
            ->first();

        if (! $assignClass) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found.',
            ]);
        }

        $subject = $assignClass->subjects->first();

        // Check whether this teacher has a replacement for this class today
        $replacement = ClassReplacement::where(
            'assign_class_id',
            $assignClass->id
        )
            ->where(
                'replacement_teacher_id',
                $teacher->id
            )
            ->whereDate(
                'date',
                $realDate
            )
            ->first();

        if (! $subject) {
            return response()->json([
                'success' => false,
                'message' => 'No subject is assigned to this class.',
            ]);
        }

        // Determine whether this is a replacement class
        if ($replacement) {

            // Replacement class time
            $classStartTime = Carbon::parse(
                $replacement->start_time
            );

            $classEndTime = Carbon::parse(
                $replacement->end_time
            );

        } else {

            // Permanent class time
            $classStartTime = Carbon::parse(
                $assignClass->start_time
            );

            $classEndTime = Carbon::parse(
                $assignClass->end_time
            );
        }

        $currentTimeOnly = Carbon::parse($realTime);

        // Before class start time
        if ($currentTimeOnly->lt($classStartTime)) {

            return response()->json([
                'success' => false,
                'type' => 'not_started',
                'message' => 'Attendance will be started from '.
                    '<strong>'.
                    $classStartTime->format('h:i A').
                    '</strong>.',
            ]);
        }

        // After class end time
        if ($currentTimeOnly->gte($classEndTime)) {

            return response()->json([
                'success' => false,
                'type' => 'time_ended',
                'message' => 'Attendance was allowed only from<br>'.
                    '<strong>'.
                    $classStartTime->format('h:i A').
                    '</strong> to '.
                    '<strong>'.
                    $classEndTime->format('h:i A').
                    '</strong>.',
            ]);
        }

        // Before class start time
        if ($currentTimeOnly->lt($classStartTime)) {
            return response()->json([
                'success' => false,
                'type' => 'not_started',
                'message' => 'Attendance will be started from '.
            '<strong>'.$classStartTime->format('h:i A').'</strong>.',
            ]);
        }

        // After class end time
        if ($currentTimeOnly->gte($classEndTime)) {
            return response()->json([
                'success' => false,
                'type' => 'time_ended',
                'message' => 'Attendance was allowed only from<br>'.
                   '<strong>'.$classStartTime->format('h:i A').'</strong> to '.
                    '<strong>'.$classEndTime->format('h:i A').'</strong>.',
            ]);
        }

        // Check if today's session already exists
        $sessionQuery = AttendanceSession::where(
            'assign_class_id',
            $assignClass->id
        )
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->whereDate(
                'date',
                $realDate
            );

        if ($replacement) {

            $sessionQuery->where(
                'replacement_id',
                $replacement->id
            );

        } else {

            $sessionQuery->whereNull(
                'replacement_id'
            );
        }

        $session = $sessionQuery->first();

        // Check if today's session already open
        if ($session) {

            if ($session->status === 'Open') {

                $sessionEnd = Carbon::parse($session->date)
                    ->setTimeFromTimeString(
                        Carbon::parse($session->end_time)->format('H:i:s')
                    )
                    ->setTimezone('Asia/Kathmandu');

                if ($currentTime->greaterThanOrEqualTo($sessionEnd)) {

                    $this->markAbsentStudents(
                        $assignClass,
                        $teacher->id,
                        $realDate
                    );

                    $session->update([
                        'status' => 'Closed',
                    ]);

                    return response()->json([
                        'success' => true,
                        'type' => 'closed',
                        'message' => 'Attendance period has ended. Students who did not scan have been marked Absent.',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'type' => 'open',
                ]);
            }

            // Session is already closed
            if ($session->status === 'Closed') {

                return response()->json([
                    'success' => true,
                    'type' => 'closed',
                    'message' => 'The attendance session for this class has already ended. Students who did not scan their QR code have been marked Absent.',
                ]);
            }
        }

        // No session = allow a new attendance
        return response()->json([
            'success' => true,
            'type' => 'new',
        ]);
    }

    // Create Attendance Session
    public function createSession(Request $request, RealTimeService $realTimeService)
    {
        $realNow = $realTimeService->now();

        if (! $realNow) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify the current date and time. Please check your internet connection and try again.',
            ]);
        }

        $realDate = $realNow->format('Y-m-d');
        $realTime = $realNow->format('H:i:s');

        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found.',
            ]);
        }

        $assignClass = Assignclass::with('subjects')
            ->where('id', $request->assign_class_id)
            ->first();

        if (! $assignClass) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found or you are not assigned to this class.',
            ]);
        }

        $subject = $assignClass->subjects->first();

        $replacement = ClassReplacement::where(
            'assign_class_id',
            $assignClass->id
        )
            ->where(
                'replacement_teacher_id',
                $teacher->id
            )
            ->whereDate(
                'date',
                $realDate
            )
            ->first();

        if (! $subject) {
            return response()->json([
                'success' => false,
                'message' => 'No subject is assigned to this class.',
            ]);
        }

        $currentTimeOnly = Carbon::parse($realTime);

        if ($replacement) {

            $classStartTime = Carbon::parse(
                $replacement->start_time
            );

            $classEndTime = Carbon::parse(
                $replacement->end_time
            );

        } else {

            $classStartTime = Carbon::parse(
                $assignClass->start_time
            );

            $classEndTime = Carbon::parse(
                $assignClass->end_time
            );
        }

        if ($currentTimeOnly->lt($classStartTime)) {
            return response()->json([
                'success' => false,
                'type' => 'not_started',
                'message' => 'Attendance will be started from'.
            '<strong>'.$classStartTime->format('h:i A').'</strong>.',
            ]);
        }

        if ($currentTimeOnly->gte($classEndTime)) {
            return response()->json([
                'success' => false,
                'type' => 'time_ended',
                'message' => 'Attendance time has ended. Attendance was allowed only from '.
                    $classStartTime->format('h:i A').' to '.
                    $classEndTime->format('h:i A').'.',
            ]);
        }

        $sessionQuery = AttendanceSession::where(
            'assign_class_id',
            $assignClass->id
        )
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->whereDate(
                'date',
                $realDate
            );

        if ($replacement) {

            $sessionQuery->where(
                'replacement_id',
                $replacement->id
            );

        } else {

            $sessionQuery->whereNull(
                'replacement_id'
            );
        }

        $existingSession = $sessionQuery->first();

        // Do not create another session if one already exists
        if ($existingSession) {

            if ($existingSession->status === 'Open') {
                return response()->json([
                    'success' => true,
                    'type' => 'open',
                ]);
            }

            if ($existingSession->status === 'Closed') {
                return response()->json([
                    'success' => false,
                    'type' => 'closed',
                    'message' => 'The attendance session for this class has already ended.',
                ]);
            }
        }

        $startTime = Carbon::parse($assignClass->start_time)
            ->setDate(
                Carbon::parse($realDate)->year,
                Carbon::parse($realDate)->month,
                Carbon::parse($realDate)->day
            );

        $endTime = Carbon::parse($assignClass->end_time)
            ->setDate(
                Carbon::parse($realDate)->year,
                Carbon::parse($realDate)->month,
                Carbon::parse($realDate)->day
            );

        AttendanceSession::create([
            'assign_class_id' => $assignClass->id,
            'replacement_id' => $replacement?->id,
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
        $subject = $assignClass->subjects->first();

        if (! $subject) {
            return;
        }

        $students = Students::where(
            'current_semester',
            $assignClass->semester
        )->get();

        foreach ($students as $student) {

            $exists = Attendance::where('student_id', $student->id)
                ->where('teacher_id', $teacherId)
                ->where('subject_id', $subject->id)
                ->where('assign_class_id', $assignClass->id)
                ->whereDate('date', $date)
                ->exists();

            if (! $exists) {

                Attendance::create([
                    'semester' => $student->current_semester,
                    'student_id' => $student->id,
                    'teacher_id' => $teacherId,
                    'subject_id' => $subject->id,
                    'assign_class_id' => $assignClass->id,
                    'date' => $date,
                    'time' => null,
                    'status' => 'Absent',
                ]);
            }
        }
    }

    // Get Attendance Count
    public function getAttendanceCount(Request $request, RealTimeService $realTimeService)
    {
        $realNow = $realTimeService->now();

        if (! $realNow) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify the current date and time. Please check your internet connection.',
            ]);
        }

        $realDate = $realNow->format('Y-m-d');

        // Logged in teacher
        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found.',
            ]);
        }

        // Selected class
        $assignClass = Assignclass::with('subjects')
            ->where('id', $request->assign_class_id)
            ->first();

        if (! $assignClass) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found.',
            ]);
        }

        // Get subject
        $subject = $assignClass->subjects->first();

        if (! $subject) {
            return response()->json([
                'success' => false,
                'message' => 'No subject assigned to this class.',
            ]);
        }

        $replacement = null;

        if ($request->filled('replacement_id')) {

            $replacement = ClassReplacement::where(
                'id',
                $request->replacement_id
            )
                ->where(
                    'assign_class_id',
                    $assignClass->id
                )
                ->where(
                    'replacement_teacher_id',
                    $teacher->id
                )
                ->whereDate(
                    'date',
                    $realDate
                )
                ->first();

            if (! $replacement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid replacement class.',
                ]);
            }
        }

        $presentQuery = Attendance::where(
            'assign_class_id',
            $assignClass->id
        )
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->whereDate(
                'date',
                $realDate
            )
            ->where(
                'status',
                'Present'
            );

        if ($replacement) {

            $presentQuery->where(
                'replacement_id',
                $replacement->id
            );

        } else {

            $presentQuery->whereNull(
                'replacement_id'
            );
        }

        $present = $presentQuery->count();

        $absentQuery = Attendance::where(
            'assign_class_id',
            $assignClass->id
        )
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->whereDate(
                'date',
                $realDate
            )
            ->where(
                'status',
                'Absent'
            );

        if ($replacement) {

            $absentQuery->where(
                'replacement_id',
                $replacement->id
            );

        } else {

            $absentQuery->whereNull(
                'replacement_id'
            );
        }

        $absent = $absentQuery->count();

        $total = Students::where(
            'current_semester',
            $assignClass->semester
        )->count();

        return response()->json([
            'success' => true,
            'present' => $present,
            'absent' => $absent,
            'total' => $total,
        ]);
    }
}
