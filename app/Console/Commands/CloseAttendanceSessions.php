<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\AttendanceSession;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
use App\Models\Admin\Students;

class CloseAttendanceSessions extends Command
{
    protected $signature = 'attendance:close';

    protected $description = 'Automatically close expired attendance sessions';

    public function handle()
    {
        $sessions = AttendanceSession::where('status', 'Open')
            ->where('end_time', '<=', now())
            ->get();

        foreach ($sessions as $session) {

            $assignClass = Assignclass::with('subjects')->find($session->assign_class_id);

            if (!$assignClass) {
                continue;
            }

            $this->markAbsentStudents($assignClass, $session);

            $session->update([
                'status' => 'Closed'
            ]);
        }

        return self::SUCCESS;
    }

    private function markAbsentStudents($assignClass, $session)
    {
        $subject = $assignClass->subjects->first();

        if (!$subject) {
            return;
        }

        $students = Students::where('current_semester', $assignClass->semester)->get();

        foreach ($students as $student) {

            $exists = Attendance::where('student_id', $student->id)
                ->where('teacher_id', $session->teacher_id)
                ->where('subject_id', $subject->id)
                ->whereDate('date', $session->date)
                ->exists();

            if (!$exists) {

                Attendance::create([
                    'semester' => $student->current_semester,
                    'student_id' => $student->id,
                    'teacher_id' => $session->teacher_id,
                    'subject_id' => $subject->id,
                    'date' => $session->date,
                    'time' => $session->end_time->format('H:i:s'),
                    'status' => 'Absent',
                ]);
            }
        }
    }
}