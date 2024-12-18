<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolDiscipline extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'school_id',
        'discipline_id',
        // 'coordinator_id',
        // 'workload'
    ];

    protected $casts = [
        // 'workload' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    // Relacionamento com SchoolLevelDiscipline
    public function schoolLevelDisciplines()
    {
        return $this->hasMany(SchoolLevelDiscipline::class, 'discipline_id', 'discipline_id')
            ->where('school_id', $this->school_id);
    }

    // Relacionamento com SchoolCourseDiscipline
    public function schoolCourseDisciplines()
    {
        return $this->hasMany(SchoolCourseDiscipline::class, 'discipline_id', 'discipline_id')
            ->where('school_id', $this->school_id);
    }

    public function levels()
    {
        return $this->hasManyThrough(
            Level::class,
            SchoolLevelDiscipline::class,
            'discipline_id',
            'id',          
            'discipline_id',
            'level_id'     
        )->distinct();
    }

    public function courses()
    {
        return $this->hasManyThrough(
            Course::class,
            SchoolCourseDiscipline::class,
            'discipline_id',
            'id',          
            'discipline_id',
            'course_id'    
        )->distinct();
    }
}