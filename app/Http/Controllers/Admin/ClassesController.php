<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

class ClassesController extends Controller
{
    public function classes()
    {
        return view('admin.classes', [
            'pageTitle' => 'Classes'
        ]);
    }
}