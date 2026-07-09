<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    protected $table = 'subjects';

    protected $fillable = [
        'subject_name',
        'subject_code',
        'semester',
    ];

    public function teachers()
    {
        return $this->belongsToMany(
            Teachers::class,
            'subject_teacher',
            'subject_id',
            'teacher_id'
        );
    }

    public function assignClasses()
    {
        return $this->belongsToMany(
            Assignclass::class,
            'assign_class_subject',
            'subject_id',
            'assign_class_id'
        );
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'subject_id');
    }
}
