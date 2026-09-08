<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
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

                    // semester search
                    if (is_numeric($search)) {
                        $q->orWhere('semester', $search);
                    }

                    // subject name search
                    $q->orWhereHas('subjects', function ($subject) use ($search) {
                        $subject->where('subject_name', 'like', '%'.$search.'%');
                    });
                });
            })
            ->orderBy('semester', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(10)
            ->withQueryString();

        $today = Carbon::today()->toDateString();

        $replacements = ClassReplacement::where(
            'replacement_teacher_id',
            $teacher->id
        )
            ->whereDate('date', $today)
            ->get()
            ->keyBy('assign_class_id');

        foreach ($assignclasses as $assignclass) {
            Students::where(
                'current_semester',
                $assignclass->semester
            )->count();

            // Original Time
            $assignclass->display_start_time =
                $assignclass->start_time;

            $assignclass->display_end_time =
                $assignclass->end_time;

            $assignclass->is_replacement_today = false;

            // Replace TIme
            if ($replacements->has($assignclass->id)) {

                $replacement = $replacements->get($assignclass->id);

                $assignclass->display_start_time =
                    $replacement->start_time;

                $assignclass->display_end_time =
                    $replacement->end_time;

                $assignclass->is_replacement_today = true;
            }

        }

        return view('teacher.assignclass', [
            'pageTitle' => 'Assigned Classes',
            'teacher' => $teacher,
            'assignclasses' => $assignclasses,
        ]);
    }
}
