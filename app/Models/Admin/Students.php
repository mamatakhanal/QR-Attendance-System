<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'name',
        'roll_no',
        'email',
        'password',
        'phone',
        'gender',
        'dob',
        'address',
        'current_semester',
        'admission_year',
        'student_code',

    ];

    // Student Academic Information
    public function academic()
    {
        return $this->hasOne(StudentAcademic::class, 'student_id');
    }


    public function assignclasses()
    {
        return $this->belongsToMany(
            Assignclass::class,
            'assign_class_subject'
        );
    }

    // Student Attendance Records
    // public function attendance()
    // {
    //     return $this->hasMany(StudentAttendance::class, 'student_id');
    // }
}
