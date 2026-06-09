<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

class AssignclassController extends Controller
{
    public function assignclass()
    {
        return view('admin.assignclass', [
            'pageTitle' => 'Assign Class'
        ]);
    }
}