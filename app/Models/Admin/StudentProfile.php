<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $table = 'student_profile';

    protected $fillable = [
        'student_id',
        'address',
        'phone',
        'gender',
        'dob'
    ];
}

   