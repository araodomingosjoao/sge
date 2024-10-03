<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends BaseModel
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'discipline_id',
        'classroom_id',
        'day',
        'start_time',
        'end_time'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
