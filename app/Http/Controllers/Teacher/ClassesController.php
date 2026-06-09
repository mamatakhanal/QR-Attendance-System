<?php
namespace App\Http\Controllers\Teacher;
use App\Http\Controllers\Controller;

class ClassesController extends Controller
{
    public function classes()
    {
        return view('teacher.classes', [
            'pageTitle' => 'Classes'
        ]);
    }
}