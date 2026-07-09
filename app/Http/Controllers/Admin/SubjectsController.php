<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Subjects;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectsController extends Controller
{

    public function subjects(Request $request)
    {
        $search = $request->search;
        $subjects = Subjects::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject_name', 'like', "%{$search}%")
                    ->orWhere('subject_code', 'like', "%{$search}%")
                    ->orWhere('semester', 'like', "%{$search}%");
            });
        });

        // Semester filter
        if ($request->semester && $request->semester != 'all') {
            $subjects->where(
                'semester',
                $request->semester
            );
        }

        // Semester wise + code number order
        $subjects = $subjects
            ->orderBy('semester', 'asc')
            ->orderByRaw('CAST(RIGHT(subject_code, 3) AS UNSIGNED) ASC')
            ->paginate(10)
            ->withQueryString();

        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }

        return view('admin.subjects', [
            'pageTitle' => 'Subjects',
            'subjects' => $subjects,
            'admin' => $admin
        ]);
    }

    // Create Subject
    public function create(Request $request)
    {
        // Validation
        $request->validate([
            'subject_name' => 'required|string|max:100',
            'subject_code' => 'required|regex:/^[A-Za-z0-9\-_#+]+$/|max:20|unique:subjects,subject_code',
            'semester' => 'required|integer|between:1,8',
        ], [
            'subject_code.unique' => 'Subject code already exists.',
            'subject_code.regex' => 'Code must contain only letters and numbers without spaces.',
        ]);

        // Create Subjects after Checking 
        $subjects = Subjects::create([
            'subject_name' => $request->subject_name,
            'subject_code' => $request->subject_code,
            'semester' => $request->semester,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subject added successfully'
        ]);
    }

    // Edit Subject
    public function update(Request $request, $id)
    {

        $subject = Subjects::findOrFail($id);

        // Validation
        $request->validate([
            'subject_name' => 'required|string|max:100',
            'subject_code' => [
                'required',
                'regex:/^[A-Za-z0-9\-_#+]+$/',
                'max:20',
                Rule::unique('subjects', 'subject_code')->ignore($id),
            ],
            'semester' => 'required|integer|between:1,8',
        ], [
            'subject_code.regex' => 'Code must contain only letters and numbers without spaces.',
        ]);

        $subject->update([
            'subject_name' => $request->subject_name,
            'subject_code' => $request->subject_code,
            'semester' => $request->semester,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subject updated successfully'
        ]);
    }


    // Delete Subject
    public function delete($id)
    {
        $subject = Subjects::findOrFail($id);

        // Check if subject is assigned to any teacher/class
        if ($subject->assignClasses()->exists()) {
            return redirect()->back()->with(
                'error',
                'Assigned subject cannot be deleted.'
            );
        }

        $subject->delete();
        return redirect()->back()->with(
            'success',
            'Subject deleted successfully.'
        );
    }
}
