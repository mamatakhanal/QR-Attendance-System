<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    protected $table = 'subjects';

    protected $fillable = [
        'subject_name',
        'semester',
    ];

    // public function teachers()
    // {
    //     return $this->belongsToMany(
    //         Teachers::class,
    //         'subject_teacher',
    //         'subject_id',
    //         'teacher_id'
    //     );
    // }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'subject_id');
    }
}