<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\ClassReplacement;

class AttendanceSession extends Model
{
    protected $table = 'attendance_sessions';

    protected $fillable = [
        'assign_class_id',
        'replacement_id',
        'teacher_id',
        'subject_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relationship with Assign Class
    public function assignClass()
    {
        return $this->belongsTo(Assignclass::class, 'assign_class_id');
    }

    // Relationship with Teacher
    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }

    // Relationship with Subject
    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    public function replacement()
    {
        return $this->belongsTo(
            ClassReplacement::class,
            'replacement_id'
        );
    }
}
