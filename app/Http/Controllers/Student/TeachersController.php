<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;

class TeachersController extends Controller
{
    public function teachers()
    {
        return view('student.teachers', [
            'pageTitle' => 'Teachers'
        ]);
    }
}