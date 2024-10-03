<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discipline extends BaseModel
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = ['name'];

    protected $casts = [
        'id' => 'string',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_disciplines');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_discipline_class')
                    ->withPivot('class_id')
                    ->withTimestamps();
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'teacher_discipline_class')
                    ->withPivot('teacher_id')
                    ->withTimestamps();
    }
}
