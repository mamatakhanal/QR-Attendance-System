<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Assignclass;
use App\Models\Admin\AttendanceSession;
use App\Models\Admin\ClassReplacement;
use App\Models\Admin\Subjects;
use App\Models\Admin\Teachers;
use Carbon\Carbon;
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

        $subjects = Subjects::orderBy('semester')
            ->orderBy('subject_name')
            ->get();

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
            ->orderBy('start_time', 'desc')
            ->orderBy('end_time', 'desc')
            ->paginate(10)
            ->withQueryString();

        $today = Carbon::today();
        $now = Carbon::now();

        foreach ($replacements as $replacement) {

            $replacementDate = Carbon::parse($replacement->date);

            // Future class
            if ($replacementDate->isAfter($today)) {

                $replacement->attendance_status = 'Scheduled';
                $replacement->attendance_status_class = 'secondary';
                $replacement->can_edit = true;
                $replacement->can_delete = true;

                continue;
            }

            // Past date
            if ($replacementDate->isBefore($today)) {

                $replacement->attendance_status = 'Time Expired';
                $replacement->attendance_status_class = 'danger';
                $replacement->can_edit = false;
                $replacement->can_delete = false;

                continue;
            }

            // Today's attendance session
            $session = AttendanceSession::where(
                'replacement_id',
                $replacement->id
            )
                ->whereDate('date', $replacement->date)
                ->latest('id')
                ->first();

            // Attendance completed
            if ($session && $session->status === 'Closed') {

                $replacement->attendance_status = 'Attendance Done';
                $replacement->attendance_status_class = 'success';
                $replacement->can_edit = false;
                $replacement->can_delete = false;

                continue;
            }

            // Attendance currently running
            if ($session && $session->status === 'Open') {

                $replacement->attendance_status = 'Attendance In Progress';
                $replacement->attendance_status_class = 'warning';
                $replacement->can_edit = false;
                $replacement->can_delete = false;

                continue;
            }

            // No attendance session yet
            $startTime = Carbon::parse(
                $replacement->date.' '.$replacement->start_time
            );

            $endTime = Carbon::parse(
                $replacement->date.' '.$replacement->end_time
            );

            // Before or during scheduled time
            if ($now->lte($endTime)) {

                $replacement->attendance_status = 'Not Taken';
                $replacement->attendance_status_class = 'danger';
                $replacement->can_edit = true;
                $replacement->can_delete = true;

            } else {

                // Time finished and no attendance session
                $replacement->attendance_status = 'Time Expired';
                $replacement->attendance_status_class = 'danger';
                $replacement->can_edit = false;
                $replacement->can_delete = false;
            }
        }

        if ($request->ajax()) {

            return view('admin.classreplacement', [
                'replacements' => $replacements,
                'assignClasses' => $assignClasses,
                'teachers' => $teachers,
                'subjects' => $subjects,
                'pageTitle' => 'Class Replacement',
            ])->render();
        }

        return view('admin.classreplacement', [
            'admin' => $admin,
            'replacements' => $replacements,
            'assignClasses' => $assignClasses,
            'teachers' => $teachers,
            'subjects' => $subjects,
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
                'after_or_equal:10:00',
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

        // Find the original assigned class for this subject and semester
        $originalClass = Assignclass::where(
            'semester',
            $subject->semester
        )
            ->whereHas('subjects', function ($query) use ($subject) {
                $query->where('subjects.id', $subject->id);
            })
            ->first();

        if ($originalClass) {

            // Check whether attendance has already been taken
            // for this original class today.
            $existingAttendance = AttendanceSession::where(
                'assign_class_id',
                $originalClass->id
            )
                ->whereDate('date', $request->date)
                ->latest('id')
                ->first();

            if ($existingAttendance) {

                if ($existingAttendance->status === 'Closed') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Class has already been completed.',
                    ], 422);
                }

                if ($existingAttendance->status === 'Open') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Class is currently in progress.',
                    ], 422);
                }
            }
        }

        $hasSemesterConflict = ClassReplacement::whereDate(
            'date',
            $request->date
        )
            ->whereHas('subject', function ($query) use ($subject) {
                $query->where('semester', $subject->semester);
            })
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

        if ($hasSemesterConflict) {

            return response()->json([
                'success' => false,
                'message' => 'This semester already has a class during this time.',
            ], 422);
        }

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
                'message' => 'This subject already has a replacement on this date.',
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
                'message' => 'This teacher already has another replacement class during the selected time on this date.',
            ], 422);
        }

        $teacherAssignedClass = Assignclass::where(
            'teacher_id',
            $request->replacement_teacher_id
        )
            ->where(
                'semester',
                $subject->semester
            )
            ->whereHas('subjects', function ($query) use ($request) {
                $query->where(
                    'subjects.id',
                    $request->subject_id
                );
            })
            ->first();

        if (! $teacherAssignedClass) {
            return response()->json([
                'success' => false,
                'message' => 'This teacher is not assigned to this class and subject.',
            ], 422);
        }

        // Check whether replacement teacher already has a permanent class
        // during the selected time.
        $hasPermanentClass = Assignclass::where(
            'teacher_id',
            $request->replacement_teacher_id
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

        if ($hasPermanentClass) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher is already assigned to another class at this time.',
            ], 422);
        }

        ClassReplacement::create([
            'assign_class_id' => $teacherAssignedClass->id,
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
            'replacementTeacher',
        ])->findOrFail($id);

        return response()->json([
            'id' => $replacement->id,

            'subject_id' => $replacement->subject_id,

            'semester' => $replacement->subject?->semester,

            'subject' => $replacement->subject?->subject_name,

            'replacement_teacher_id' => $replacement->replacement_teacher_id,

            'date' => Carbon::parse($replacement->date)->format('Y-m-d'),

            'start_time' => Carbon::parse(
                $replacement->start_time
            )->format('H:i'),

            'end_time' => Carbon::parse(
                $replacement->end_time
            )->format('H:i'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::find(session('admin_id'));

        if (! $admin) {

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);

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

        ], [

            'subject_id.required' => 'Please select a subject.',

            'subject_id.exists' => 'Selected subject does not exist.',

            'replacement_teacher_id.required' => 'Please select a replacement teacher.',

            'replacement_teacher_id.exists' => 'Selected replacement teacher does not exist.',

            'date.required' => 'Please select replacement date.',

            'date.after_or_equal' => 'Replacement date cannot be before today.',

            'start_time.required' => 'Please select start time.',

            'start_time.after_or_equal' => 'Start time cannot be before 10:00 AM.',

            'start_time.before_or_equal' => 'Start time cannot be after 5:00 PM.',

            'end_time.required' => 'Please select end time.',

            'end_time.before_or_equal' => 'End time cannot be after 5:00 PM.',

            'end_time.after' => 'End time must be after start time.',

        ]);

        $subject = Subjects::findOrFail(
            $request->subject_id
        );

        // Find the original assigned class for this subject and semester
        $originalClass = Assignclass::where(
            'semester',
            $subject->semester
        )
            ->whereHas('subjects', function ($query) use ($subject) {
                $query->where('subjects.id', $subject->id);
            })
            ->first();

        if ($originalClass) {

            // Check whether attendance has already been taken
            // for this original class on the selected date.
            $existingAttendance = AttendanceSession::where(
                'assign_class_id',
                $originalClass->id
            )
                ->whereDate('date', $request->date)
                ->latest('id')
                ->first();

            if ($existingAttendance) {

                if ($existingAttendance->status === 'Closed') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Class has already been completed.',
                    ], 422);
                }

                if ($existingAttendance->status === 'Open') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Class is currently in progress.',
                    ], 422);
                }
            }
        }

        $teacherAssignedClass = Assignclass::where(
            'teacher_id',
            $request->replacement_teacher_id
        )
            ->where(
                'semester',
                $subject->semester
            )
            ->whereHas('subjects', function ($query) use ($request) {
                $query->where(
                    'subjects.id',
                    $request->subject_id
                );
            })
            ->first();

        if (! $teacherAssignedClass) {
            return response()->json([
                'success' => false,
                'message' => 'This teacher is not assigned to this class and subject.',
            ], 422);
        }
        // Check whether replacement teacher already has a permanent class
        // during the selected time.
        $hasPermanentClass = Assignclass::where(
            'teacher_id',
            $request->replacement_teacher_id
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

        if ($hasPermanentClass) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher is already assigned to another class at this time.',
            ], 422);
        }

        $hasSemesterConflict = ClassReplacement::whereDate(
            'date',
            $request->date
        )
            ->whereHas('subject', function ($query) use ($subject) {
                $query->where('semester', $subject->semester);
            })
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

        if ($hasSemesterConflict) {

            return response()->json([
                'success' => false,
                'message' => 'This semester already has a class during this time.',
            ], 422);
        }

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

            return response()->json([
                'success' => false,
                'message' => 'This subject already has a replacement on this date.',
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

            return response()->json([
                'success' => false,
                'message' => 'The replacement teacher already has another replacement class during this time.',
            ], 422);

        }

        $replacement->update([
            'assign_class_id' => $teacherAssignedClass->id,
            'subject_id' => $request->subject_id,
            'replacement_teacher_id' => $request->replacement_teacher_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return response()->json([

            'success' => true,

            'message' => 'Class replacement updated successfully.',

        ]);
    }

    public function delete($id)
    {
        ClassReplacement::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Class replacement deleted successfully');
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
