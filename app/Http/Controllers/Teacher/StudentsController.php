<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Teachers;
use App\Models\Admin\Students;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    public function students(Request $request)
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (!$teacher) {
            return redirect('/home');
        }

        // Get teacher assigned semesters
        $assignedSemesters = $teacher->assignclasses()
            ->pluck('semester')
            ->unique()
            ->sort();

        $students = Students::whereIn('current_semester', $assignedSemesters)
            ->when($request->semester && $request->semester != 'all', function ($query) use ($request) {
                $query->where('current_semester', $request->semester);
            })

            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%")
                        ->orWhere('roll_no', 'like', "%{$search}%");
                    if (is_numeric($search)) {
                        $q->orWhere('current_semester', $search);
                    }
                });
            })
            ->orderBy('current_semester', 'asc')
            ->orderByRaw('CAST(roll_no AS UNSIGNED) ASC')
            ->paginate(10);

        return view('teacher.students', [
            'pageTitle' => 'Students',
            'teacher' => $teacher,
            'students' => $students,
            'assignedSemesters' => $assignedSemesters
        ]);
    }
}
