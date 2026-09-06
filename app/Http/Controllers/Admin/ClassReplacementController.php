<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Assignclass;
use App\Models\Admin\ClassReplacement;
use App\Models\Admin\Subjects;
use App\Models\Admin\Teachers;
use Illuminate\Http\Request;

class ClassReplacementController extends Controller
{
    public function classreplacement(Request $request)
    {
        $admin = Admin::find(session('admin_id'));

        if (! $admin) {
            return redirect('/admin/login');
        }

        $assignClasses = Assignclass::with([
            'teacher',
            'subjects',
        ])
            ->orderBy('semester')
            ->get();

        $teachers = Teachers::orderBy('name')->get();

        $replacements = ClassReplacement::with([
            'subject',
            'assignclass.teacher',
            'replacementTeacher',
        ])
            ->when(
                $request->filled('semester') &&
                $request->semester !== 'all',
                function ($query) use ($request) {

                    $query->whereHas('subject', function ($q) use ($request) {
                        $q->where('semester', $request->semester);
                    });
                }
            )
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {

                    $search = $request->search;

                    $query->where(function ($q) use ($search) {

                        $q->whereHas(
                            'replacementTeacher',
                            function ($teacher) use ($search) {

                                $teacher->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                            }
                        )
                            ->orWhereHas(
                                'subject',
                                function ($subject) use ($search) {

                                    $subject->where(
                                        'subject_name',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            );
                    });
                }
            )
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {

            return view('admin.classreplacement', [
                'replacements' => $replacements,
                'assignClasses' => $assignClasses,
                'teachers' => $teachers,
                'pageTitle' => 'Class Replacement',
            ])->render();
        }

        return view('admin.classreplacement', [
            'admin' => $admin,
            'replacements' => $replacements,
            'assignClasses' => $assignClasses,
            'teachers' => $teachers,
            'pageTitle' => 'Class Replacement',
        ]);
    }

    public function store(Request $request)
    {
        $admin = Admin::find(session('admin_id'));

        if (! $admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $request->validate([
            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],

            'replacement_teacher_id' => [
                'required',
                'exists:teachers,id',
            ],

            'date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
                'after_or_equal:10:00',
                'before_or_equal:17:00',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'before_or_equal:17:00',
                'after:start_time',
            ],
        ], [

            'subject_id.required' => 'Please select a subject.',

            'subject_id.exists' => 'Selected subject does not exist.',

            'replacement_teacher_id.required' => 'Please select a replacement teacher.',

            'date.after_or_equal' => 'Replacement date cannot be before today.',

            'start_time.after_or_equal' => 'Start time cannot be before 10:00 AM.',

            'start_time.before_or_equal' => 'Start time cannot be after 5:00 PM.',

            'end_time.before_or_equal' => 'End time cannot be after 5:00 PM.',

            'end_time.after' => 'End time must be after start time.',
        ]);

        $subject = Subjects::findOrFail(
            $request->subject_id
        );

        $assignClass = Assignclass::where(
            'semester',
            $subject->semester
        )
            ->whereHas('subjects', function ($query) use ($subject) {

                $query->where(
                    'subjects.id',
                    $subject->id
                );
            })
            ->first();

        $assignClassId = $assignClass?->id;

        $today = now()->toDateString();

        if ($request->date === $today) {

            $currentTime = now()->format('H:i');

            if ($request->start_time < $currentTime) {

                return response()->json([
                    'success' => false,
                    'message' => 'For today, replacement class start time cannot be earlier than the current time.',
                ], 422);
            }
        }

        $existingReplacement = ClassReplacement::where(
            'subject_id',
            $request->subject_id
        )
            ->whereDate(
                'date',
                $request->date
            )
            ->exists();

        if ($existingReplacement) {

            return response()->json([
                'success' => false,
                'message' => 'A replacement already exists for this subject on the selected date.',
            ], 422);
        }

        $hasReplacementClass = ClassReplacement::where(
            'replacement_teacher_id',
            $request->replacement_teacher_id
        )
            ->whereDate(
                'date',
                $request->date
            )
            ->where(function ($query) use ($request) {

                $query->where(
                    'start_time',
                    '<',
                    $request->end_time
                )
                    ->where(
                        'end_time',
                        '>',
                        $request->start_time
                    );
            })
            ->exists();

        if ($hasReplacementClass) {

            return response()->json([
                'success' => false,
                'message' => 'The replacement teacher already has another replacement class during this time.',
            ], 422);
        }

        // if (
        //     $assignClass->teacher_id ==
        //     $request->replacement_teacher_id
        // ) {

        //     return response()->json([
        //         'success' => false,
        //         'message' => 'The original teacher cannot be selected as the replacement teacher.',
        //     ], 422);
        // }
        // $hasPermanentClass = Assignclass::where(
        //     'teacher_id',
        //     $request->replacement_teacher_id
        // )
        //     ->where(function ($query) use ($request) {

        //         $query->where(
        //             'start_time',
        //             '<',
        //             $request->end_time
        //         )
        //             ->where(
        //                 'end_time',
        //                 '>',
        //                 $request->start_time
        //             );

        //     })
        //     ->exists();

        // if ($hasPermanentClass) {

        //     return response()->json([
        //         'success' => false,
        //         'message' => 'The replacement teacher already has another class during this time.',
        //     ], 422);
        // }

        ClassReplacement::create([
            'assign_class_id' => $assignClassId,

            'subject_id' => $request->subject_id,

            'replacement_teacher_id' => $request->replacement_teacher_id,

            'date' => $request->date,

            'start_time' => $request->start_time,

            'end_time' => $request->end_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Class replacement created successfully.',
        ]);
    }

    public function edit($id)
    {
        $replacement = ClassReplacement::with([
            'subject',
            'assignclass',
            'replacementTeacher',
        ])->findOrFail($id);

        return response()->json([
            'id' => $replacement->id,

            'subject_id' => $replacement->subject_id,

            'semester' => $replacement->subject->semester ?? '',

            'subject' => $replacement->subject->subject_name ?? '',

            'assign_class_id' => $replacement->assign_class_id,

            'replacement_teacher_id' => $replacement->replacement_teacher_id,

            'date' => $replacement->date,

            'start_time' => substr($replacement->start_time, 0, 5),

            'end_time' => substr($replacement->end_time, 0, 5),
        ]);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::find(session('admin_id'));

        if (! $admin) {
            return redirect('/admin/login');
        }

        $replacement = ClassReplacement::findOrFail($id);

        $request->validate([
            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],

            'replacement_teacher_id' => [
                'required',
                'exists:teachers,id',
            ],

            'date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
                'after_or_equal:10:00',
                'before_or_equal:17:00',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'before_or_equal:17:00',
                'after:start_time',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Today Time Validation
        |--------------------------------------------------------------------------
        */
        $today = now()->toDateString();

        if ($request->date === $today) {

            $currentTime = now()->format('H:i');

            if ($request->start_time < $currentTime) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'For today, replacement class start time cannot be earlier than the current time.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Get Subject
        |--------------------------------------------------------------------------
        */
        $subject = Subjects::findOrFail(
            $request->subject_id
        );

        /*
        |--------------------------------------------------------------------------
        | Find Assign Class
        |--------------------------------------------------------------------------
        */
        $assignClass = Assignclass::where(
            'semester',
            $subject->semester
        )
            ->whereHas('subjects', function ($query) use ($subject) {

                $query->where(
                    'subjects.id',
                    $subject->id
                );
            })
            ->first();

        $assignClassId = $assignClass?->id;

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate replacement for same subject/date
        |--------------------------------------------------------------------------
        */
        $existingReplacement = ClassReplacement::where(
            'subject_id',
            $request->subject_id
        )
            ->whereDate(
                'date',
                $request->date
            )
            ->where(
                'id',
                '!=',
                $replacement->id
            )
            ->exists();

        if ($existingReplacement) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'A replacement already exists for this subject on the selected date.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Replacement teacher overlap
        |--------------------------------------------------------------------------
        |
        | Only replacement classes are checked.
        |
        | Normal Assignclass classes are NOT checked.
        |
        */
        $hasReplacementClass = ClassReplacement::where(
            'replacement_teacher_id',
            $request->replacement_teacher_id
        )
            ->whereDate(
                'date',
                $request->date
            )
            ->where(
                'id',
                '!=',
                $replacement->id
            )
            ->where(function ($query) use ($request) {

                $query->where(
                    'start_time',
                    '<',
                    $request->end_time
                )
                    ->where(
                        'end_time',
                        '>',
                        $request->start_time
                    );
            })
            ->exists();

        if ($hasReplacementClass) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'The replacement teacher already has another replacement class during this time.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */
        $replacement->update([
            'assign_class_id' => $assignClassId,

            'subject_id' => $request->subject_id,

            'replacement_teacher_id' => $request->replacement_teacher_id,

            'date' => $request->date,

            'start_time' => $request->start_time,

            'end_time' => $request->end_time,
        ]);

        return redirect()
            ->route('admin.classreplacement')
            ->with(
                'success',
                'Class replacement updated successfully.'
            );
    }

    /**
     * Delete replacement.
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
            ->with(
                'success',
                'Class replacement deleted successfully.'
            );
    }

    public function destroy($id)
    {
        $replacement = ClassReplacement::findOrFail($id);

        $replacement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Class replacement deleted successfully.',
        ]);
    }
}
