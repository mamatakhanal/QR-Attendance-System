<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
use App\Models\Admin\AttendanceSession;
use App\Models\Admin\ClassReplacement;
use App\Models\Admin\Students;
use App\Models\Admin\Teachers;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssignclassController extends Controller
{
    public function assignclass(Request $request)
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (! $teacher) {
            return redirect('/home');
        }

        $search = $request->search;
        $semester = $request->semester;

        $assignclasses = Assignclass::with('subjects')
            ->where('teacher_id', $teacher->id)

            ->when($semester, function ($query) use ($semester) {
                $query->where('semester', $semester);
            })

            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    // Semester search
                    if (is_numeric($search)) {
                        $q->orWhere('semester', $search);
                    }

                    // Subject name search
                    $q->orWhereHas('subjects', function ($subject) use ($search) {
                        $subject->where(
                            'subject_name',
                            'like',
                            '%'.$search.'%'
                        );
                    });
                });
            })

            ->orderBy('semester', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Today's replacements
        |--------------------------------------------------------------------------
        */

        $today = Carbon::today()->toDateString();

        $replacements = ClassReplacement::whereDate('date', $today)
            ->whereIn(
                'assign_class_id',
                $assignclasses->pluck('id')
            )
            ->get()
            ->keyBy('assign_class_id');

        /*
        |--------------------------------------------------------------------------
        | Prepare class information
        |--------------------------------------------------------------------------
        */

        foreach ($assignclasses as $assignclass) {

            // Student count
            $assignclass->student_count = Students::where(
                'current_semester',
                $assignclass->semester
            )->count();

            // Default attendance status
            $assignclass->attendance_status = 'Not Taken';

            /*
            |--------------------------------------------------------------------------
            | Check today's attendance session
            |--------------------------------------------------------------------------
            */

            $session = AttendanceSession::where(
                'assign_class_id',
                $assignclass->id
            )
                ->where(
                    'teacher_id',
                    $teacher->id
                )
                ->whereDate(
                    'date',
                    $today
                )
                ->latest('id')
                ->first();

            if ($session) {

                if ($session->status === 'Open') {

                    $assignclass->attendance_status = 'In Progress';

                } elseif ($session->status === 'Closed') {

                    $assignclass->attendance_status = 'Taken';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Normal Class Time
            |--------------------------------------------------------------------------
            */

            $assignclass->display_start_time = $assignclass->start_time;
            $assignclass->display_end_time = $assignclass->end_time;

            $assignclass->is_replacement_today = false;

            /*
            |--------------------------------------------------------------------------
            | Replacement Class Time
            |--------------------------------------------------------------------------
            */

            if ($replacements->has($assignclass->id)) {

                $replacement = $replacements->get($assignclass->id);

                $assignclass->replacement_start_time =
                    $replacement->start_time;

                $assignclass->replacement_end_time =
                    $replacement->end_time;

                $assignclass->is_replacement_today = true;
            }

            /*
            |--------------------------------------------------------------------------
            | Block Permanent Class
            |
            | If another replacement overlaps this permanent class,
            | the permanent class becomes Blocked.
            |--------------------------------------------------------------------------
            */

            if (! $assignclass->is_replacement_today) {

                $blocked = $replacements->contains(
                    function ($replacement) use ($assignclass) {

                        $replacementStart = Carbon::parse(
                            $replacement->start_time
                        );

                        $replacementEnd = Carbon::parse(
                            $replacement->end_time
                        );

                        $classStart = Carbon::parse(
                            $assignclass->start_time
                        );

                        $classEnd = Carbon::parse(
                            $assignclass->end_time
                        );

                        return $replacement->assign_class_id != $assignclass->id
                            && $replacementStart->lt($classEnd)
                            && $replacementEnd->gt($classStart);
                    }
                );

                if ($blocked) {

                    $assignclass->attendance_status = 'Blocked';
                }
            }
        }

        return view('teacher.assignclass', [
            'pageTitle' => 'Assigned Classes',
            'teacher' => $teacher,
            'assignclasses' => $assignclasses,

            // IMPORTANT
            'replacements' => $replacements,
        ]);
    }
}
