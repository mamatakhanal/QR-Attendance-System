<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Attendance;
use App\Models\Admin\Teachers;
use App\Models\Admin\Subjects;
use Illuminate\Http\Request;
use App\Models\Admin\Admin;

class AttendanceController extends Controller
{
    public function attendance(Request $request)
    {
        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        $attendances = Attendance::with([
            'teacher',
            'student',
            'subject'
        ])
            ->when($request->teacher_id, function ($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            })
            ->when($request->semester, function ($q) use ($request) {
                $q->where('semester', $request->semester);
            })
            ->when($request->subject_id, function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->date, function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            })
            ->when($request->search, function ($q) use ($request) {

                $q->whereHas('student', function ($student) use ($request) {

                    $student->where('name', 'like', "%{$request->search}%")
                        ->orWhere('roll_no', 'like', "%{$request->search}%")
                        ->orWhere('student_code', 'like', "%{$request->search}%");
                });
            })
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.attendance', [
            'pageTitle' => 'Attendance',
            'admin' => $admin,
            'attendances' => $attendances,
            'teachers' => Teachers::orderBy('name')->get(),
            'subjects' => Subjects::orderBy('subject_name')->get(),
        ]);
    }
}
