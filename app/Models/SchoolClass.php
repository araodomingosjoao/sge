<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends BaseModel
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = "classes";
    protected $fillable = ['course_id', 'level_id', 'name'];

    protected $casts = [
        'id' => 'string',
        'course_id' => 'string',
        'level_id' => 'string',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class, 'teacher_discipline_class')
                    ->withPivot('teacher_id')
                    ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_discipline_class')
                    ->withPivot('discipline_id')
                    ->withTimestamps();
    }
}
