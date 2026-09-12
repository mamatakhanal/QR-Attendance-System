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
            abort(500, 'Unable to get current time.');
        }

        $realNow = $realNow->copy()->setTimezone('Asia/Kathmandu');

        $realDate = $realNow->format('Y-m-d');
        $realTime = $realNow->format('H:i:s');

        $currentDateTime = $realNow;

        $oldSessions = AttendanceSession::where('teacher_id', $teacher->id)
            ->where('status', 'Open')
            ->get();

        foreach ($oldSessions as $oldSession) {

            $sessionEnd = Carbon::parse(
                $oldSession->end_time,
                'Asia/Kathmandu'
            );

            if ($currentDateTime->greaterThanOrEqualTo($sessionEnd)) {

                $assignClass = Assignclass::with('subjects')
                    ->find($oldSession->assign_class_id);

                if ($assignClass) {
                    $this->markAbsentStudents(
                        $assignClass,
                        $oldSession->teacher_id,
                        $oldSession->date,
                        $oldSession->replacement_id
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

        // ALWAYS Kathmandu
        $realNow = $realNow->copy()->setTimezone('Asia/Kathmandu');

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
            ->where('teacher_id', $teacher->id)
            ->first();

        if (! $assignClass) {
            return response()->json([
                'success' => false,
                'message' => 'The selected class could not be found. Please select a class and try again.',
            ]);
        }

        $subject = $assignClass->subjects->first();

        if (! $subject) {
            return response()->json([
                'success' => false,
                'message' => 'No subject is assigned to this class.',
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
                'message' => 'Attendance session has not been started. Please start the attendance session first.',
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

        if ($attendanceQuery->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This student\'s attendance has already been marked for today.',
            ]);
        }

        // $attendance = $attendanceQuery->first();
        // if ($attendance) {
        //     if ($attendance->status == 'Present') {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'This student\'s attendance has already been marked for today.',
        //         ]);
        //     }
        //     if ($attendance->status == 'Absent') {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'The Attendance period has ended. <br> <br> Students who did not scan their QR code within the session have been marked <strong> Absent</strong>.',
        //         ]);
        //     }
        // }

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

        $realNow = $realNow->copy()->setTimezone('Asia/Kathmandu');

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
            ->where('teacher_id', $teacher->id)
            ->first();

        if (! $assignClass) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found.',
            ]);
        }

        $subject = $assignClass->subjects->first();

        if (! $subject) {
            return response()->json([
                'success' => false,
                'message' => 'No subject is assigned to this class.',
            ]);
        }

        // Check whether this teacher has a replacement for this class today
        $replacement = null;

        if ($request->filled('replacement_id')) {

            $replacement = ClassReplacement::where('id', $request->replacement_id)
                ->where('assign_class_id', $assignClass->id)
                ->where('replacement_teacher_id', $teacher->id)
                ->whereDate('date', $realDate)
                ->first();

            if (! $replacement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid replacement class.',
                ]);
            }
        }

        // Determine whether this is a replacement class
        if ($replacement) {

            $classStartTime = Carbon::createFromFormat(
                'H:i:s',
                Carbon::parse($replacement->start_time)->format('H:i:s'),
                'Asia/Kathmandu'
            );

            $classEndTime = Carbon::createFromFormat(
                'H:i:s',
                Carbon::parse($replacement->end_time)->format('H:i:s'),
                'Asia/Kathmandu'
            );

        } else {

            $classStartTime = Carbon::createFromFormat(
                'H:i:s',
                Carbon::parse($assignClass->start_time)->format('H:i:s'),
                'Asia/Kathmandu'
            );

            $classEndTime = Carbon::createFromFormat(
                'H:i:s',
                Carbon::parse($assignClass->end_time)->format('H:i:s'),
                'Asia/Kathmandu'
            );
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

        $existingSession = $sessionQuery->first();

        // If attendance session already exists
        if ($existingSession && $existingSession->status === 'Open') {
            return response()->json([
                'success' => true,
                'type' => 'open',
            ]);
        }

        // Session was already completed
        if ($existingSession && $existingSession->status === 'Closed') {
            return response()->json([
                'success' => false,
                'type' => 'closed',
                'message' => 'The attendance session for this class has ended.<br><br>
                          Students who did not scan their QR code within the attendance
                          period have been marked <strong>Absent</strong>.',
            ]);
        }

        $currentTime = $realNow;

        $classStart = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $realDate.' '.$classStartTime->format('H:i:s'),
            'Asia/Kathmandu'
        );

        $classEnd = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $realDate.' '.$classEndTime->format('H:i:s'),
            'Asia/Kathmandu'
        );

        if ($currentTime->lt($classStart)) {

            return response()->json([
                'success' => false,
                'type' => 'not_started',
                'message' => 'Attendance will be available from <strong>'.
                    $classStart->format('h:i A').
                    '</strong> to '. '<strong>'.
                        $classEndTime->format('h:i A').
                        '</strong>.',
            ]);
        }

        if ($currentTime->gte($classEnd)) {

            return response()->json([
                'success' => false,
                'type' => 'time_ended',
                'message' => 'Attendance was available from<br>'.
                        '<strong>'.
                        $classStartTime->format('h:i A').
                        '</strong> to '.
                        '<strong>'.
                        $classEndTime->format('h:i A').
                        '</strong>.',
            ]);
        }

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

        $realNow = $realNow->copy()->setTimezone('Asia/Kathmandu');

        $realDate = $realNow->format('Y-m-d');

        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found.',
            ]);
        }

        $assignClass = Assignclass::with('subjects')
            ->where('id', $request->assign_class_id)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (! $assignClass) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found or you are not assigned to this class.',
            ]);
        }

        $subject = $assignClass->subjects->first();
        if (! $subject) {
            return response()->json([
                'success' => false,
                'message' => 'No subject is assigned to this class.',
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

        if ($replacement) {

            $startTime = Carbon::parse($replacement->start_time)
                ->format('H:i:s');

            $endTime = Carbon::parse($replacement->end_time)
                ->format('H:i:s');

        } else {

            $startTime = Carbon::parse($assignClass->start_time)
                ->format('H:i:s');

            $endTime = Carbon::parse($assignClass->end_time)
                ->format('H:i:s');
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
                    'message' => 'Attendance has already been completed for this class today.',
                ]);
            }
        }

        // Use replacement class time if this is a replacement,
        // otherwise use the permanent assigned class time.
        if ($replacement) {

            $startTime = Carbon::parse($replacement->start_time)
                ->setDate(
                    Carbon::parse($realDate)->year,
                    Carbon::parse($realDate)->month,
                    Carbon::parse($realDate)->day
                );

            $endTime = Carbon::parse($replacement->end_time)
                ->setDate(
                    Carbon::parse($realDate)->year,
                    Carbon::parse($realDate)->month,
                    Carbon::parse($realDate)->day
                );

        } else {

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
        }

        $session = AttendanceSession::firstOrCreate(
            [
                'assign_class_id' => $assignClass->id,
                'replacement_id' => $replacement?->id,
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id,
                'date' => $realDate,
            ],
            [
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'status' => 'Open',
            ]
        );

        return response()->json([
            'success' => true,
            'type' => 'open',
        ]);
    }

    // Mark Absent Students
    public function markAbsentStudents($assignClass, $teacherId, $date, $replacementId = null)
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

            $attendanceQuery = Attendance::where(
                'student_id',
                $student->id
            )
                ->where(
                    'teacher_id',
                    $teacherId
                )
                ->where(
                    'subject_id',
                    $subject->id
                )
                ->where(
                    'assign_class_id',
                    $assignClass->id
                )
                ->whereDate(
                    'date',
                    $date
                );

            if ($replacementId !== null) {
                $attendanceQuery->where(
                    'replacement_id',
                    $replacementId
                );
            } else {
                $attendanceQuery->whereNull(
                    'replacement_id'
                );
            }

            // Student already has attendance record
            if ($attendanceQuery->exists()) {
                continue;
            }

            Attendance::create([
                'semester' => $student->current_semester,
                'student_id' => $student->id,
                'teacher_id' => $teacherId,
                'subject_id' => $subject->id,
                'assign_class_id' => $assignClass->id,
                'replacement_id' => $replacementId,
                'date' => $date,
                'time' => null,
                'status' => 'Absent',
            ]);
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

        $realNow = $realNow->copy()->setTimezone('Asia/Kathmandu');
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
            ->where('teacher_id', $teacher->id)
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

        $present = Attendance::where(
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

        if ($request->filled('replacement_id')) {
            $present->where('replacement_id', $request->replacement_id);
        } else {
            $present->whereNull('replacement_id');
        }

        $present = $present->distinct('student_id')->count('student_id');

        $absent = Attendance::where(
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
        if ($request->filled('replacement_id')) {
            $absent->where('replacement_id', $request->replacement_id);
        } else {
            $absent->whereNull('replacement_id');
        }

        $absent = $absent->distinct('student_id')->count('student_id');

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
