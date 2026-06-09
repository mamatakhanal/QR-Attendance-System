<?php
namespace App\Http\Controllers\Teacher;
use App\Http\Controllers\Controller;

class StudentsController extends Controller
{
    public function students()
    {
        return view('teacher.students', [
            'pageTitle' => 'Students'
        ]);
    }
}