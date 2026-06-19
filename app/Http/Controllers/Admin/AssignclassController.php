<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Teachers;
use App\Models\Admin\Subjects;
use Illuminate\Http\Request;

class AssignclassController extends Controller
{
    public function assignclass()
    {
        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        $assignclasses = Teachers::with('subjects')->paginate();

        $teachers = Teachers::all();
        $subjects = Subjects::all();

        return view('admin.assignclass', [
            'pageTitle' => 'Assign Class',
            'admin' => $admin,
            'assignclasses' => $assignclasses,
            'teachers' => $teachers,
            'subjects' => $subjects
        ]);
    }


    public function create(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required',
            'semester' => 'required',
            'subject_ids' => 'required|array'
        ]);

        $teacher = Teachers::findOrFail($request->teacher_id);
        foreach ($request->subject_ids as $subjectId) {
            // duplicate data
            $exists = $teacher->subjects()
                ->wherePivot('subject_id', $subjectId)
                ->wherePivot('semester', $request->semester)
                ->exists();

            if (!$exists) {
                $teacher->subjects()->attach($subjectId, [
                    'semester' => $request->semester
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Subject assigned successfully'
        ]);
    }

    // Delete 
    public function delete($teacher_id, $subject_id)
    {
        $teacher = Teachers::findOrFail($teacher_id);
        $teacher->subjects()->detach($subject_id);
        return redirect()->back()->with('success', 'Deleted sucessfully');
    }


    // Get Subjects after chossing semester
    public function getSubjects($semester)
    {
        $subjects = Subjects::where('semester', (int)$semester)->get();
        return response()->json($subjects);
    }
}
