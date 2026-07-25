<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
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

    public function scanAttendance(Request $request)
    {
        // Logged in teacher
        $teacher = Teachers::find(session('teacher_id'));
        if (! $teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR Code..',
            ]);
        }

        // Selected class
        $assignClass = Assignclass::with('subjects')->find($request->assign_class_id);
        if (! $assignClass) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR Code..',
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
                'message' => 'Invalid QR Code.',
            ]);
        }

        // Find student
        $student = Students::find($qr['student_id']);
        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR Code.',
            ]);
        }

        // Check student belongs to selected semester
        if ($student->current_semester != $assignClass->semester) {
            return response()->json([
                'success' => false,
                'message' => 'This student does not belong to the selected class.',
            ]);
        }

        // Check if attendance already marked today
        $alreadyMarked = Attendance::where('student_id', $student->id)
            ->where('teacher_id', $teacher->id)
            ->where('subject_id', $assignClass->subjects->first()->id)
            ->whereDate('date', now()->toDateString())
            ->exists();

        if ($alreadyMarked) {
            return response()->json([
                'success' => false,
                'message' => 'This student\'s attendance has already been marked for today.',
            ]);
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
}
