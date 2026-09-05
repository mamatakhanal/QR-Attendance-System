<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Assignclass;
use App\Models\Admin\ClassReplacement;
use App\Models\Admin\Teachers;
use Illuminate\Http\Request;

class ClassReplacementController extends Controller
{
    /**
     * Display class replacement records.
     */
    public function classreplacement(Request $request)
    {
        $admin = Admin::find(session('admin_id'));

        if (! $admin) {
            return redirect('/admin/login');
        }

        // Existing assigned classes for Create/Edit modal
        $assignClasses = Assignclass::with([
            'teacher',
            'subjects',
        ])
            ->orderBy('semester')
            ->get();

        // All teachers for replacement teacher dropdown
        $teachers = Teachers::orderBy('name')->get();

        // Replacement records
        $replacements = ClassReplacement::with([
            'assignclass.subjects',
            'originalTeacher',
            'replacementTeacher',
        ])
            // Semester filter
            ->when(
                $request->filled('semester') && $request->semester !== 'all',
                function ($query) use ($request) {
                    $query->whereHas('assignclass', function ($q) use ($request) {
                        $q->where('semester', $request->semester);
                    });
                }
            )

            // Search filter
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = $request->search;

                    $query->where(function ($q) use ($search) {

                        // Original teacher
                        $q->whereHas('originalTeacher', function ($teacher) use ($search) {
                            $teacher->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        })

                        // Replacement teacher
                        ->orWhereHas('replacementTeacher', function ($teacher) use ($search) {
                            $teacher->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        })

                        // Subject
                        ->orWhereHas('assignclass.subjects', function ($subject) use ($search) {
                            $subject->where(
                                'subject_name',
                                'like',
                                "%{$search}%"
                            );
                        });

                    });
                }
            )

            // Latest date first
            ->orderBy('date', 'desc')

            // Earlier class first when same date
            ->orderBy('start_time', 'asc')

            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | AJAX Request
        |--------------------------------------------------------------------------
        */

        if ($request->ajax()) {
            return view('admin.classreplacement', [
                'replacements' => $replacements,
                'assignClasses' => $assignClasses,
                'teachers' => $teachers,
                'pageTitle' => 'Class Replacement',
            ])->render();
        }

        /*
        |--------------------------------------------------------------------------
        | Normal Request
        |--------------------------------------------------------------------------
        */

        return view('admin.classreplacement', [
            'admin' => $admin,
            'replacements' => $replacements,
            'assignClasses' => $assignClasses,
            'teachers' => $teachers,
            'pageTitle' => 'Class Replacement',
        ]);
    }


    /**
     * Store a new class replacement.
     */
    public function store(Request $request)
    {
        $admin = Admin::find(session('admin_id'));

        if (! $admin) {
            return redirect('/admin/login');
        }

        $request->validate([
            'assign_class_id' => 'required|exists:assign_class,id',
            'original_teacher_id' => 'required|exists:teachers,id',
            'replacement_teacher_id' => 'required|exists:teachers,id|different:original_teacher_id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:255',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate replacement for same class and date
        |--------------------------------------------------------------------------
        */

        $existingReplacement = ClassReplacement::where(
            'assign_class_id',
            $request->assign_class_id
        )
            ->whereDate('date', $request->date)
            ->exists();

        if ($existingReplacement) {
            return back()
                ->withInput()
                ->with('error', 'A replacement already exists for this class on the selected date.');
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent replacement teacher from having overlapping permanent class
        |--------------------------------------------------------------------------
        */

        $hasPermanentClass = Assignclass::where(
            'teacher_id',
            $request->replacement_teacher_id
        )
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($hasPermanentClass) {
            return back()
                ->withInput()
                ->with('error', 'The replacement teacher already has another class during this time.');
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent replacement teacher from having overlapping replacement
        |--------------------------------------------------------------------------
        */

        $hasReplacementClass = ClassReplacement::where(
            'replacement_teacher_id',
            $request->replacement_teacher_id
        )
            ->whereDate('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($hasReplacementClass) {
            return back()
                ->withInput()
                ->with('error', 'The replacement teacher already has another replacement class during this time.');
        }

        /*
        |--------------------------------------------------------------------------
        | Create replacement
        |--------------------------------------------------------------------------
        */

        ClassReplacement::create([
            'assign_class_id' => $request->assign_class_id,
            'original_teacher_id' => $request->original_teacher_id,
            'replacement_teacher_id' => $request->replacement_teacher_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
        ]);

        return redirect()
            ->route('admin.classreplacement')
            ->with('success', 'Class replacement created successfully.');
    }


    /**
     * Get replacement data for Edit modal.
     */
    public function edit($id)
    {
        $replacement = ClassReplacement::with([
            'assignclass.subjects',
            'originalTeacher',
            'replacementTeacher',
        ])->findOrFail($id);

        return response()->json([
            'id' => $replacement->id,

            'assign_class_id' => $replacement->assign_class_id,

            'semester' => $replacement->assignclass->semester ?? '',

            'subject' => $replacement->assignclass->subjects->first()?->subject_name ?? '',

            'original_teacher_id' => $replacement->original_teacher_id,

            'original_teacher_name' =>
                $replacement->originalTeacher->name ?? '',

            'replacement_teacher_id' =>
                $replacement->replacement_teacher_id,

            'date' => $replacement->date,

            'start_time' =>
                substr($replacement->start_time, 0, 5),

            'end_time' =>
                substr($replacement->end_time, 0, 5),

            'reason' => $replacement->reason ?? '',
        ]);
    }


    /**
     * Update an existing class replacement.
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::find(session('admin_id'));

        if (! $admin) {
            return redirect('/admin/login');
        }

        $replacement = ClassReplacement::findOrFail($id);

        $request->validate([
            'assign_class_id' => 'required|exists:assign_class,id',
            'original_teacher_id' => 'required|exists:teachers,id',
            'replacement_teacher_id' => 'required|exists:teachers,id|different:original_teacher_id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:255',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate replacement
        |--------------------------------------------------------------------------
        */

        $existingReplacement = ClassReplacement::where(
            'assign_class_id',
            $request->assign_class_id
        )
            ->whereDate('date', $request->date)
            ->where('id', '!=', $replacement->id)
            ->exists();

        if ($existingReplacement) {
            return back()
                ->withInput()
                ->with('error', 'A replacement already exists for this class on the selected date.');
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent permanent teacher time conflict
        |--------------------------------------------------------------------------
        */

        $hasPermanentClass = Assignclass::where(
            'teacher_id',
            $request->replacement_teacher_id
        )
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($hasPermanentClass) {
            return back()
                ->withInput()
                ->with('error', 'The replacement teacher already has another class during this time.');
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent replacement time conflict
        |--------------------------------------------------------------------------
        */

        $hasReplacementClass = ClassReplacement::where(
            'replacement_teacher_id',
            $request->replacement_teacher_id
        )
            ->whereDate('date', $request->date)
            ->where('id', '!=', $replacement->id)
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($hasReplacementClass) {
            return back()
                ->withInput()
                ->with('error', 'The replacement teacher already has another replacement class during this time.');
        }

        /*
        |--------------------------------------------------------------------------
        | Update replacement
        |--------------------------------------------------------------------------
        */

        $replacement->update([
            'assign_class_id' => $request->assign_class_id,
            'original_teacher_id' => $request->original_teacher_id,
            'replacement_teacher_id' => $request->replacement_teacher_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
        ]);

        return redirect()
            ->route('admin.classreplacement')
            ->with('success', 'Class replacement updated successfully.');
    }


    /**
     * Delete a class replacement.
     */
    public function delete($id)
    {
        $admin = Admin::find(session('admin_id'));

        if (! $admin) {
            return redirect('/admin/login');
        }

        $replacement = ClassReplacement::findOrFail($id);

        $replacement->delete();

        return redirect()
            ->route('admin.classreplacement')
            ->with('success', 'Class replacement deleted successfully.');
    }
}