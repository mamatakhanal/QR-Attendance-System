<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Admin\Teachers;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Students;

class AttendanceController extends Controller
{

    public function attendance()
    {
        $teacher = Teachers::find(session('teacher_id'));

        if (!$teacher) {
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
            'assignclasses' => $assignclasses
        ]);
    }
}
