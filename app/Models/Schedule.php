<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['teacher_discipline_class_id', 'day_of_week', 'start_time', 'end_time', 'classroom', 'specific_date'];

    public function teacherDisciplineClass()
    {
        return $this->belongsTo(TeacherDisciplineClass::class);
    }
}
