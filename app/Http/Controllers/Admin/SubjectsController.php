<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

class SubjectsController extends Controller
{
    public function subjects()
    {
        return view('admin.subjects', [
            'pageTitle' => 'Subjects'
        ]);
    }
}