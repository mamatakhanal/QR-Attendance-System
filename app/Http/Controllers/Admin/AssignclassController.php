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
    public function assignclass(Request $request)
    {
        $search = $request->search;
        $semester = $request->semester;

        $assignclasses = Assignclass::with(['teacher', 'subjects'])
            // Semester filter
            ->when($semester && $semester != 'all', function ($query) use ($semester) {
                $query->where('semester', $semester);
            })

            // Search
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('teacher', function ($t) use ($search) {
                        $t->where('name', 'like', "%{$search}%");
                    })
                        ->orWhereHas('subjects', function ($s) use ($search) {
                            $s->where('subject_name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('semester', 'asc')
            ->paginate(10)
            ->withQueryString();

        $admin = Admin::find(session('admin_id'));
        if (!$admin) {
            return redirect('/admin/login');
        }

        $teachers = Teachers::orderBy('name', 'asc')->get();
        $subjects = Subjects::pluck('subject_name', 'id');

        if ($request->ajax()) {

            return view('admin.assignclass', [
                'assignclasses' => $assignclasses,
                'teachers' => $teachers,
                'subjects' => $subjects,
                'pageTitle' => 'Assign Class'
            ])->render();
        }

        return view('admin.assignclass', [
            'pageTitle' => 'Assign Classes',
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
            'teacher_id'  => 'required',
            'semester'    => 'required',
            'subject_ids' => 'required|array|size:1',
        ], [
            'subject_ids.size' => 'Please select only one subject.',
        ]);

        $teacherId = $request->teacher_id;
        $semester  = $request->semester;

        // Teacher already assigned in this semester
        $exists = Assignclass::where('teacher_id', $teacherId)
            ->where('semester', $semester)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher is already assigned to this semester.'
            ]);
        }

        // Subjects already assigned to teacher
        $subjectId = $request->subject_ids[0];
        $subjectAssigned = Assignclass::where('semester', $semester)
            ->whereHas('subjects', function ($q) use ($subjectId) {
                $q->where('subjects.id', $subjectId);
            })
            ->exists();

        if ($subjectAssigned) {
            return response()->json([
                'success' => false,
                'message' => 'This subject is already assigned to another teacher.'
            ]);
        }

        $assignclass = Assignclass::create([
            'teacher_id' => $teacherId,
            'semester'   => $semester,
        ]);

        $assignclass->subjects()->attach($request->subject_ids);

        return response()->json([
            'success' => true,
            'message' => 'Assignment created successfully.'
        ]);
    }


    // Update 
    public function update(Request $request, $id)
    {
        $request->validate([
            'teacher_id'  => 'required',
            'semester'    => 'required',
            'subject_ids' => 'required|array|size:1',
        ], [
            'subject_ids.size' => 'Please select only one subject.',
        ]);

        $exists = Assignclass::where('teacher_id', $request->teacher_id)
            ->where('semester', $request->semester)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher is already assigned to this semester.'
            ]);
        }

        // Subject already assigned to teacher
        $subjectId = $request->subject_ids[0];
        $subjectAssigned = Assignclass::where('semester', $request->semester)
            ->where('id', '!=', $id)
            ->whereHas('subjects', function ($q) use ($subjectId) {
                $q->where('subjects.id', $subjectId);
            })
            ->exists();

        if ($subjectAssigned) {
            return response()->json([
                'success' => false,
                'message' => 'This subject is already assigned to another teacher.'
            ]);
        }

        $assignclass = Assignclass::findOrFail($id);

        $assignclass->update([
            'teacher_id' => $request->teacher_id,
            'semester'   => $request->semester,
        ]);

        $assignclass->subjects()->sync($request->subject_ids);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully.'
        ]);
    }


    // Delete 
    public function delete($id)
    {
        Assignclass::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Deleted successfully');
    }


    // Get Subjects after chossing semester
    public function getSubjects($semester)
    {
        $subjects = Subjects::where('semester', (int)$semester)->get();
        return response()->json($subjects);
    }

    // View 
    public function viewTeacherAssignment($id)
    {
        $teacher = Teachers::findOrFail($id);


        $assignments = Assignclass::with('subjects')
            ->where('teacher_id', $id)
            // ->orderBy('semester','asc')
            ->get();


        return response()->json([
            'teacher' => $teacher,
            'assignments' => $assignments
        ]);
    }
}
