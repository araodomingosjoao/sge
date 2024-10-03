<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherDisciplineClass extends BaseModel
{
    use HasFactory, HasUuids;

    protected $table = 'teacher_discipline_class';
    protected $fillable = ['teacher_id', 'discipline_id', 'class_id'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
