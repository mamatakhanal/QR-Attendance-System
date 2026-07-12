<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Students;
use App\Models\Admin\Subjects;
use App\Models\Admin\Assignclass;

class ClassesController extends Controller
{
    public function classes()
    {
        $student = Students::find(session('student_id'));

        if (!$student) {
            return redirect('/home');
        }

        // All subjects of student's semester
        $subjects = Subjects::where('semester', $student->current_semester)
            ->orderBy('subject_name')
            ->get();

        // Assigned teachers indexed by subject_id
        $assignments = Assignclass::with('teacher', 'subjects')
            ->where('semester', $student->current_semester)
            ->get();

        $teacherBySubject = [];

        foreach ($assignments as $assignment) {
            foreach ($assignment->subjects as $subject) {
                $teacherBySubject[$subject->id] = $assignment->teacher;
            }
        }

        return view('student.classes', [
            'pageTitle' => 'Classes',
            'student' => $student,
            'subjects' => $subjects,
            'teacherBySubject' => $teacherBySubject,
        ]);
    }
}
