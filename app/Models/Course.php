<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['name', 'category_id'];

    protected $casts = [
        'id' => 'string',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class, 'level_course_disciplines')
            ->distinct();
    }

    public function schoolCourseDisciplines()
    {
        return $this->hasMany(SchoolCourseDiscipline::class);
    }

    public function schoolCourses()
    {
        return $this->hasMany(SchoolCourse::class);
    }

    public function levelCourseDisciplines()
    {
        return $this->hasMany(LevelCourseDiscipline::class);
    }
}
