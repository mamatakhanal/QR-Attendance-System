<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Assignclass;
use App\Models\Admin\Students;
use App\Models\Admin\Subjects;

class ClassesController extends Controller
{
    public function classes()
    {
        $student = Students::find(session('student_id'));

        if (! $student) {
            return redirect('/home');
        }

        // All subjects of student's semester
        $subjects = Subjects::where('semester', $student->current_semester)
            ->get();

        // Assigned teachers indexed by subject_id
        $assignments = Assignclass::with('teacher', 'subjects')
            ->where('semester', $student->current_semester)
            ->orderBy('start_time', 'asc')
            ->get();

        $assignmentBySubject = [];

        foreach ($assignments as $assignment) {
            foreach ($assignment->subjects as $subject) {
                $assignmentBySubject[$subject->id] = $assignment;
            }
        }

        $subjects = $subjects->sortBy(function ($subject) use ($assignmentBySubject) {
            return $assignmentBySubject[$subject->id]->start_time ?? '99:99:99';
        })->values();

        return view('student.classes', [
            'pageTitle' => 'Classes',
            'student' => $student,
            'subjects' => $subjects,
            'assignmentBySubject' => $assignmentBySubject,
        ]);
    }
}
