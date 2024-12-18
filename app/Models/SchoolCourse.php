<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolCourse extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'school_id',
        'course_id',
        // 'is_active',
        // 'coordinator_id',
        // 'max_students'
    ];

    protected $casts = [
        // 'is_active' => 'boolean',
        // 'start_date' => 'date',
        // 'max_students' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // public function coordinator()
    // {
    //     return $this->belongsTo(User::class, 'coordinator_id');
    // }

    public function disciplines()
    {
        return $this->hasManyThrough(
            Discipline::class,
            SchoolLevelDiscipline::class,
            'course_id',
            'id',
            'course_id',
            'discipline_id'
        )->distinct();
    }
}
