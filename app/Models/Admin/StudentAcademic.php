<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StudentAcademic extends Model
{
    protected $table = 'student_academic';

    protected $fillable = [
        'student_id',
        'semester',
        'year',
        'status'
    ];

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }
}
