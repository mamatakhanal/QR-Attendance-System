<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Assignclass extends Model
{
    protected $table = 'assign_class';

    protected $fillable = [
        'semester',
        'student_id',
        'teacher_id',
        'assign_class_id',
        'subject_id',
        'date',
        'time',
        'status',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(
            Subjects::class,
            'assign_class_subject',
            'assign_class_id',
            'subject_id'
        );
    }
}
