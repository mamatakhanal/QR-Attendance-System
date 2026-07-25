<?php

namespace App\Console\Commands;

use App\Models\Admin\Attendance;
use App\Models\Admin\AttendanceSession;
use App\Models\Admin\Students;
use Illuminate\Console\Command;

class CloseAttendanceSessions extends Command
{
    protected $signature = 'attendance:close-sessions';

    protected $description = 'Close attendance sessions after 30 minutes and mark remaining students absent';

    public function handle()
    {
        $this->info('Current Time: '.now());

        $sessions = AttendanceSession::with('assignClass.subjects')
            ->where('status', 'Open')
            ->where('end_time', '<=', now())
            ->get();

        $this->info('Sessions Found: '.$sessions->count());

        foreach ($sessions as $session) {

            $this->info("Closing Session ID: {$session->id}");

            $subject = $session->assignClass->subjects->first();

            if (! $subject) {
                $this->error('No subject found.');

                continue;
            }

            $students = Students::where(
                'current_semester',
                $session->assignClass->semester
            )->get();

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
                        'subject_id' => $subject->id,
                        'date' => $session->date,
                        'time' => now()->format('H:i:s'),
                        'status' => 'Absent',
                    ]);
                }
            }

            $session->update([
                'status' => 'Closed',
            ]);

            $this->info("Attendance Session {$session->id} Closed");
        }

        return Command::SUCCESS;
    }
}
