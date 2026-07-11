<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'semester',
        'student_id',
        'subject_id',
        'teacher_id',
        'date',
        'time',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }
}
