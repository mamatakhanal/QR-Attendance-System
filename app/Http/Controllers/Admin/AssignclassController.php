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
            'subject_ids' => 'required|array|min:1'
        ]);

        $teacherId = (int)$request->teacher_id;
        $semester  = (int)$request->semester;
        $newSubjects = array_map('intval', $request->subject_ids);

        // check existing assign class
        $assignClass = Assignclass::where('teacher_id', $teacherId)
            ->where('semester', $semester)
            ->first();

        // Same teacher + same semester exists
        if ($assignClass) {
            // already assigned subjects
            $oldSubjects = $assignClass->subjects()
                ->pluck('subjects.id')
                ->map(fn($id) => (int)$id)
                ->toArray();

            // find duplicate subjects
            $duplicate = array_intersect($oldSubjects, $newSubjects);

            if (!empty($duplicate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected subjects are already assigned'
                ]);
            }

            // only attach new subjects
            $assignClass->subjects()->attach($newSubjects);
            return response()->json([
                'success' => true,
                'message' => 'New subjects added successfully'
            ]);
        }

        //  Create main assign class
        $assignClass = Assignclass::create([
            'teacher_id' => $teacherId,
            'semester' => $semester
        ]);

        // attach subjects into pivot table
        $assignClass->subjects()->attach($newSubjects);

        return response()->json([
            'success' => true,
            'message' => 'Assignment created successfully'
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

        // JUST UPDATE SAME ROW
        $assignclass->update([
            'teacher_id' => $request->teacher_id,
            'semester' => $request->semester,
        ]);

        // update subjects in pivot table
        $assignclass->subjects()->sync($request->subject_ids);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully'
        ]);
    }

    // Delete 
    public function delete($id)
    {
        Assignclass::findOrFail($id)->delete();
        return redirect()->back()
            ->with('success', 'Deleted successfully');
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
