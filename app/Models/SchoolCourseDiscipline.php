<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolCourseDiscipline extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['school_id', 'course_id', 'discipline_id'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
}

