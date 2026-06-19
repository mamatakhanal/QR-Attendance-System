<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AssignClass extends Model
{
    protected $table = 'assign_class';

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'semester',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }
}