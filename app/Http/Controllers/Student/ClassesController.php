<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;

class ClassesController extends Controller
{
    public function classes()
    {
        return view('student.classes', [
            'pageTitle' => 'Classes'
        ]);
    }
}