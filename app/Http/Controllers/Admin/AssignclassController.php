<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Teachers;
use App\Models\Admin\Subjects;
use App\Models\Admin\Assignclass;
use Illuminate\Http\Request;

class AssignclassController extends Controller
{
    public function assignclass()
    {
        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        $assignclasses = Assignclass::with(['teacher', 'subject'])
            ->orderBy('semester', 'asc')
            ->paginate(10);
            
        $teachers = Teachers::orderBy('name', 'asc')->get();
        $subjects = Subjects::all();

        return view('admin.assignclass', [
            'pageTitle' => 'Assign Class',
            'admin' => $admin,
            'assignclasses' => $assignclasses,
            'teachers' => $teachers,
            'subjects' => $subjects
        ]);
    }

    // Create
    public function create(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required',
            'semester' => 'required',
            'subject_ids' => 'required|array'
        ]);

        $duplicate = false;
        $inserted = false;
        foreach ($request->subject_ids as $subjectId) {

            $exists = Assignclass::where('teacher_id', $request->teacher_id)
                ->where('subject_id', $subjectId)
                ->where('semester', $request->semester)
                ->exists();
            if ($exists) {
                $duplicate = true;
            } else {
                Assignclass::create([
                    'teacher_id' => $request->teacher_id,
                    'subject_id' => $subjectId,
                    'semester' => $request->semester
                ]);
                $inserted = true;
            }
        }
        if ($duplicate && !$inserted) {
            return response()->json([
                'success' => false,
                'message' => 'This teacher already has this subject assigned'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Subject assigned successfully'
        ]);
    }

    // Update 
    public function update(Request $request, $id)
    {
        $request->validate([
            'teacher_id' => 'required',
            'semester' => 'required',
            'subject_ids' => 'required|array'
        ]);

        $assignclass = Assignclass::findOrFail($id);
        $assignclass->update([
            'teacher_id' => $request->teacher_id,
            'subject_id' => $request->subject_ids[0],
            'semester' => $request->semester
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subject assigned updated successfully'
        ]);
    }

    // Delete 
    public function delete($id)
    {
        AssignClass::findOrFail($id)->delete();
        return redirect()->back()
            ->with('success', 'Deleted successfully');
    }


    // Get Subjects after chossing semester
    public function getSubjects($semester)
    {
        $subjects = Subjects::where('semester', (int)$semester)->get();
        return response()->json($subjects);
    }
}
