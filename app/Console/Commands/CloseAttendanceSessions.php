<?php

namespace App\Console\Commands;

use App\Models\Admin\Assignclass;
use App\Models\Admin\Attendance;
use App\Models\Admin\AttendanceSession;
use App\Models\Admin\Students;
use App\Services\RealTimeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseAttendanceSessions extends Command
{
    protected $signature = 'attendance:close';

    protected $description = 'Automatically close expired attendance sessions and mark remaining students absent';

    public function handle(RealTimeService $realTimeService)
    {
        $this->info('Attendance scheduler started');

        $realNow = $realTimeService->now();

        if (! $realNow) {
            $this->error('Unable to get real Nepal date/time.');
            Log::error('Unable to get real Nepal date/time.');

            return self::FAILURE;
        }

        $currentDateTime = $realNow->copy()
            ->setTimezone('Asia/Kathmandu');

        $realDate = $currentDateTime->format('Y-m-d');
        $realTime = $currentDateTime->format('H:i:s');

        $this->info(
            'Nepal current time: '.
            $currentDateTime->format('Y-m-d h:i:s A')
        );

        $this->info(
            'Nepal current time: '.$currentDateTime->format('Y-m-d h:i:s A')
        );

        $sessions = AttendanceSession::where('status', 'Open')->get();

        $this->info('Open attendance sessions: '.$sessions->count());

        if ($sessions->isEmpty()) {
            $this->info('No open attendance sessions found.');

            return self::SUCCESS;
        }

        foreach ($sessions as $session) {

            // Get the Admin assigned class
            $assignClass = Assignclass::with('subjects')
                ->find($session->assign_class_id);

            if (! $assignClass) {

                $this->warn(
                    'Assign class not found for Session ID '.$session->id
                );

                $session->update([
                    'status' => 'Closed',
                ]);

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | USE ADMIN ASSIGNED CLASS END TIME
            |--------------------------------------------------------------------------
            */

            $sessionDate = Carbon::parse($session->date)
                ->setTimezone('Asia/Kathmandu');

            $classEndTime = Carbon::parse(
                $assignClass->end_time
            )->format('H:i:s');

            $sessionEnd = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $sessionDate->format('Y-m-d').' '.$classEndTime,
                'Asia/Kathmandu'
            );

            $this->info(
                'Session '.$session->id.
                ' | Class '.$assignClass->id.
                ' | Admin End Time: '.$sessionEnd->format('h:i A').
                ' | Current: '.$currentDateTime->format('h:i A')
            );

            /*
            |--------------------------------------------------------------------------
            | CLASS HAS NOT ENDED YET
            |--------------------------------------------------------------------------
            */

            if ($currentDateTime->lt($sessionEnd)) {

                $this->info(
                    'Session '.$session->id.' is still active.'
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | CLASS TIME HAS ENDED
            |--------------------------------------------------------------------------
            */

            $this->info(
                'Session '.$session->id.
                ' has expired. Marking remaining students Absent.'
            );

            /*
            |--------------------------------------------------------------------------
            | MARK REMAINING STUDENTS ABSENT
            |--------------------------------------------------------------------------
            */

            $absentCount = $this->markAbsentStudents(
                $assignClass,
                $session
            );

            /*
            |--------------------------------------------------------------------------
            | CLOSE ATTENDANCE SESSION
            |--------------------------------------------------------------------------
            */

            $session->update([
                'status' => 'Closed',
            ]);

            $this->info(
                'Session '.$session->id.
                ' closed. '.
                $absentCount.
                ' student(s) marked Absent.'
            );

            Log::info(
                'Session '.$session->id.' closed automatically.',
                [
                    'assign_class_id' => $assignClass->id,
                    'admin_start_time' => Carbon::parse(
                        $assignClass->start_time
                    )->format('H:i:s'),
                    'admin_end_time' => $classEndTime,
                    'session_end' => $sessionEnd->format('Y-m-d H:i:s'),
                    'current_time' => $currentDateTime->format('Y-m-d H:i:s'),
                    'absent_count' => $absentCount,
                ]
            );
        }

        $this->info('Attendance scheduler completed.');

        return self::SUCCESS;
    }

    private function markAbsentStudents($assignClass, $session)
    {
        $subject = $assignClass->subjects
            ->where('id', $session->subject_id)
            ->first();

        if (! $subject) {
            $this->warn(
                'No subject found for Assign Class ID '.$assignClass->id
            );

            return 0;
        }

        $students = Students::where(
            'current_semester',
            $assignClass->semester
        )->get();

        $this->info(
            'Students in Semester '.
            $assignClass->semester.
            ': '.
            $students->count()
        );

        $absentCount = 0;

        foreach ($students as $student) {

            $attendanceExists = Attendance::where('student_id', $student->id)
                ->where('teacher_id', $session->teacher_id)
                ->where('subject_id', $subject->id)
                ->where('assign_class_id', $assignClass->id)
                ->whereDate('date', $session->date)
                ->exists();

            if ($attendanceExists) {
                continue;
            }

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

            $absentCount++;

            Log::info(
                'Student ID '.
                $student->id.
                ' marked Absent for Session ID '.
                $session->id
            );
        }

        return $absentCount;
    }

    private function getRealDateTime()
    {
        try {
            $response = Http::connectTimeout(5)
                ->timeout(5)
                ->get(
                    'https://timeapi.io/api/time/current/zone',
                    [
                        'timeZone' => 'Asia/Kathmandu',
                    ]
                );

            if (! $response->successful()) {
                Log::error(
                    'Time API request failed. HTTP status: '.
                    $response->status()
                );

                return null;
            }

            $data = $response->json();

            if (! isset($data['date'], $data['time'])) {
                Log::error('Time API response does not contain date/time.');

                return null;
            }

            return [
                'date' => Carbon::parse($data['date'])->format('Y-m-d'),
                'time' => Carbon::parse($data['time'])->format('H:i:s'),
            ];

        } catch (\Throwable $e) {
            Log::error(
                'Real date/time API error: '.$e->getMessage()
            );

            return null;
        }
    }
}
