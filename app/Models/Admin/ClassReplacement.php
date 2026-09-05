<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ClassReplacement extends Model
{
    protected $fillable = [
        'assign_class_id',
        'original_teacher_id',
        'replacement_teacher_id',
        'date',
        'start_time',
        'end_time',
        'reason',
    ];

    public function assignclass()
    {
        return $this->belongsTo(Assignclass::class, 'assign_class_id');
    }

    public function originalTeacher()
    {
        return $this->belongsTo(Teachers::class, 'original_teacher_id');
    }

    public function replacementTeacher()
    {
        return $this->belongsTo(Teachers::class, 'replacement_teacher_id');
    }
}