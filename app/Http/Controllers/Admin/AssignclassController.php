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
        $assignclasses = Assignclass::with('teacher')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $lower = strtolower($search);
                    // 🔹 teacher search
                    $q->orWhereHas('teacher', function ($t) use ($search) {
                        $t->where('name', 'like', "%{$search}%");
                    });

                    if (is_numeric($search)) {
                        $q->orWhere('semester', (int)$search);
                    }

                    preg_match('/\d+/', $search, $matches);
                    $number = $matches[0] ?? null;

                    if (str_contains($lower, 'sem') || str_contains($lower, 'semester')) {
                        if ($number) {
                            $q->orWhere('semester', (int)$number);
                        }
                    }

                    $subjectIds = \App\Models\Admin\Subjects::where('subject_name', 'like', "%{$search}%")
                        ->pluck('id');

                    if ($subjectIds->isNotEmpty()) {
                        $q->orWhere(function ($sq) use ($subjectIds) {
                            foreach ($subjectIds as $id) {
                                $sq->orWhere('subject_ids', 'like', "%{$id}%");
                            }
                        });
                    }
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

        $teacherId = (int) $request->teacher_id;
        $semester  = (int) $request->semester;
        $newSubjects = array_map('intval', $request->subject_ids);

        $assign = Assignclass::where('teacher_id', $teacherId)
            ->where('semester', $semester)
            ->first();

        //  Same teacher & same semester already exists
        if ($assign) {
            $oldSubjects = json_decode($assign->subject_ids, true) ?? [];
            $oldSubjects = array_map('intval', $oldSubjects);

            $duplicate = array_intersect($oldSubjects, $newSubjects);

            if (!empty($duplicate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected subjects are already assigned'
                ]);
            }

            $merged = array_values(array_unique(array_merge($oldSubjects, $newSubjects)));

            $assign->update([
                'subject_ids' => json_encode($merged)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'New subjects added successfully'

            ]);
        }

        // Create New Assignment
        $created = Assignclass::create([
            'teacher_id'   => $teacherId,
            'semester'     => $semester,
            'subject_ids'  => json_encode($newSubjects)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subject assigned successfully',
            'data'    => $created
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

        // JUST UPDATE SAME ROW (NO DELETE, NO LOOP)
        $assignclass->update([
            'teacher_id' => $request->teacher_id,
            'semester' => $request->semester,
            'subject_ids' => json_encode($request->subject_ids)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully'
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
