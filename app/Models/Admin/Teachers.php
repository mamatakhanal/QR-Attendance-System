<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Assignclass;

class Teachers extends Model
{
    protected $table = 'teachers';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'dob',
        'address',
    ];

    public function subjects()
    {
        return $this->belongsToMany(
            Subjects::class,
            'subject_teacher',
            'teacher_id',
            'subject_id'
        )->withPivot('semester');
    }

    public function assignClasses()
    {
        return $this->hasMany(AssignClass::class, 'teacher_id');
    }
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'teacher_id');
    }
}
