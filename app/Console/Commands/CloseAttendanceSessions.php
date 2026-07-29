<?php

namespace App\Console\Commands;

use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
use App\Models\Admin\AttendanceSession;
use App\Models\Admin\Students;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseAttendanceSessions extends Command
{
    protected $signature = 'attendance:close';

    protected $description = 'Automatically close expired attendance sessions';

    public function handle()
    {
        Log::info('Attendance scheduler started');

        $sessions = AttendanceSession::where('status', 'Open')
        ->where('end_time', '<=', now())
            ->get();

            
        Log::info('Expired sessions: '.$sessions->count());

        foreach ($sessions as $session) {

            Log::info(
                'Session ID: '.$session->id.
                ' | End Time: '.$session->end_time.
                ' | Current Time: '.now()
            );

            $assignClass = Assignclass::with('subjects')
                ->find($session->assign_class_id);

            if (! $assignClass) {
                Log::info('Assign class not found');
                continue;
            }

            $this->markAbsentStudents($assignClass, $session);
            $session->update([
                'status' => 'Closed',
            ]);
            Log::info('Session closed successfully');
        }
        return self::SUCCESS;
    }

    private function markAbsentStudents($assignClass, $session)
    {
        $subject = $assignClass->subjects->first();

        if (! $subject) {
            return;
        }

        $students = Students::where('current_semester', $assignClass->semester)->get();

        foreach ($students as $student) {

            $exists = Attendance::where('student_id', $student->id)
                ->where('teacher_id', $session->teacher_id)
                ->where('subject_id', $subject->id)
                ->whereDate('date', $session->date)
                ->exists();

            if (! $exists) {

                Attendance::create([
                    'semester' => $student->current_semester,
                    'student_id' => $student->id,
                    'teacher_id' => $session->teacher_id,
                    'assign_class_id' => $assignClass->id,
                    'subject_id' => $subject->id,
                    'date' => $session->date,
                    'time' => null,
                    'status' => 'Absent',
                ]);
            }
        }
    }
}
