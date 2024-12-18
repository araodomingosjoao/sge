<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolLevel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'school_id',
        'level_id',
        'max_students',
    ];

    protected $casts = [
        // 'max_students' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function disciplines()
    {
        return $this->hasMany(SchoolLevelDiscipline::class, 'level_id', 'level_id')
            ->where('school_id', $this->school_id);
    }

    public function courses()
    {
        return $this->hasManyThrough(
            Course::class,
            SchoolLevelDiscipline::class,
            'level_id',
            'id',
            'level_id',
            'course_id'
        )->distinct();
    }
}
