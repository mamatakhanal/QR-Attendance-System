<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Teachers;
use App\Models\Admin\Assignclass;
use Illuminate\Http\Request;

class AssignclassController extends Controller
{
    public function assignclass(Request $request)
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (!$teacher) {
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
                        $subject->where('subject_name', 'like', '%' . $search . '%');
                    });
                });
            })
            ->orderBy('semester', 'asc')
            ->paginate(10)
            ->withQueryString();

        foreach ($assignclasses as $assignclass) {
            $assignclass->student_count = \App\Models\Admin\Students::where(
                'current_semester',
                $assignclass->semester
            )->count();
        }

        return view('teacher.assignclass', [
            'pageTitle' => 'Assigned Classes',
            'teacher' => $teacher,
            'assignclasses' => $assignclasses
        ]);
    }
}
