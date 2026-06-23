<?php

namespace App\Http\Controllers\Admin;

use App\Mail\StudentMail;
use App\Http\Controllers\Controller;
use App\Models\Admin\Students;
use App\Models\Admin\Admin;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class StudentsController extends Controller
{

    public function students(Request $request)
    {
        $search = $request->search;

        $students = Students::with(['academic'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('student_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('roll_no', 'like', "%{$search}%")
                        ->orWhere('admission_year', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                    // Semester search
                    if (preg_match('/\d+/', $search, $matches)) {
                        $sem = $matches[0];

                        $q->orWhere('current_semester', $sem)
                            ->orWhere('current_semester', 'like', "%{$sem}%");
                    }
                    
                    if (stripos($search, 'sem') !== false || stripos($search, 'semester') !== false) {
                        $q->orWhere('current_semester', 'like', "%{$search}%");
                    }
                });
            })
            //
            ->orderByRaw('CAST(current_semester AS UNSIGNED) ASC')
            ->orderByRaw('CAST(roll_no AS UNSIGNED) ASC')
            ->paginate(10)
            ->withQueryString();

        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            return redirect('/admin/login');
        }
        return view('admin.students', [
            'pageTitle' => 'Students',
            'students' => $students,
            'admin' => $admin
        ]);
    }

    // Create Student
    public function create(Request $request)
    {

        // Validation
        $request->validate([
            'name' => 'required|string|max:100|regex:/^[A-Za-z\s]+$/',
            'roll_no' => [
                'required',
                'integer',
                Rule::unique('students')
                    ->where(
                        fn($query) =>
                        $query->where('admission_year', $request->admission_year)
                    ),
            ],
            'email' => 'required|email|unique:students,email',
            'password' => 'required|min:8',
            'admission_year' => [
                'required',
                'integer',
                'min:2000',
                'max:' . now()->year,
                function ($attribute, $value, $fail) {
                    $semester = (now()->year - $value) + 1;

                    if ($semester > 8) {
                        $fail('This batch has already graduated.');
                    }
                }
            ],
        ], [
            'name.regex' => 'Name must contain only letters.',
            'roll_no.unique' => 'This roll number already exists in the selected batch.',
            'email.unique' => 'Email already exists.',
            'password.min' => 'Password must be at least 8 characters.'
        ]);

        // Student Code Calculate
        $year = substr($request->admission_year, -2);
        $studentCode = 'STU-' . $year . '-' . str_pad($request->roll_no, 3, '0', STR_PAD_LEFT);

        // Semester Calculate
        $currentYear = now()->year;
        $semester = ($currentYear - $request->admission_year) + 1;

        // Student Create
        $student =  Students::create([
            'name' => $request->name,
            'roll_no' => $request->roll_no,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'current_semester' => $semester,
            'admission_year' => $request->admission_year,
            'student_code' => $studentCode,
        ]);

        // GENERATE QR CODE AND SAVE TO STORAGE
        $qrImage = QrCode::format('png')
            ->size(300)
            ->generate($studentCode);

        Storage::disk('public')->put(
            "qr/{$studentCode}.png",
            $qrImage
        );

        return response()->json([
            'success' => true,
            'message' => 'Student added successfully'
        ]);
    }

    // Edit Student
    public function update(Request $request, $id)
    {

        $student = Students::findOrFail($id);

        // Validation
        $request->validate([
            'name' => 'required|string|max:100|regex:/^[A-Za-z\s]+$/',
            'roll_no' => [
                'required',
                'integer',
                Rule::unique('students')
                    ->where(
                        fn($query) =>
                        $query->where('admission_year', $request->admission_year)
                    )
                    ->ignore($id),
            ],
            'phone' => 'nullable|numeric|regex:/^[9][0-9]{9}$/',
            'dob' => 'nullable|date|before:-15 years',
            'email' => [
                'required',
                'email',
                Rule::unique('students')->ignore($id)
            ],
            'password' => 'nullable|min:8',
            'admission_year' => [
                'required',
                'integer',
                'min:2000',
                'max:' . now()->year,
                function ($attribute, $value, $fail) {
                    $semester = (now()->year - $value) + 1;

                    if ($semester > 8) {
                        $fail('This batch has already graduated.');
                    }
                }
            ],
        ], [
            'name.regex' => 'Name must contain only letters.',
            'roll_no.unique' => 'This roll number already exists in the selected batch.',
            'phone.numeric' => 'Phone number must contain numbers only.',
            'phone.regex' => 'Phone number must start with 9 and be exactly 10 digits.',
            'dob.before' => 'Student must be at least 15 years old.',
            'email.unique' => 'Email already exists.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        // Student Code Calculate
        $year = substr($request->admission_year, -2);
        $studentCode = 'STU-' . $year . '-' . str_pad($request->roll_no, 3, '0', STR_PAD_LEFT);

        // Semester Calculate
        $currentYear = now()->year;
        $semester = ($currentYear - $request->admission_year) + 1;

        $student->update([
            'name' => $request->name,
            'roll_no' => $request->roll_no,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'address' => $request->address,
            'email' => $request->email,
            'current_semester' => $semester,
            'admission_year' => $request->admission_year,
            'student_code' => $studentCode,
        ]);

        if ($request->filled('password')) {
            $student->update([
                'password' => Hash::make($request->password)
            ]);
        }

        Storage::disk('public')->put(
            "qr/{$studentCode}.png",
            QrCode::format('png')->size(300)->generate($studentCode)
        );

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully'
        ]);
    }

    // Delete Student
    public function delete($id)
    {
        Students::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Student deleted successfully');
    }


    public function sendEmail($id)
    {
        try {
            $student = Students::findOrFail($id);

            $plainPassword = Str::random(8);
            $student->update([
                'password' => Hash::make($plainPassword)
            ]);

            Mail::to($student->email)
                ->send(new StudentMail($student, $plainPassword));

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
