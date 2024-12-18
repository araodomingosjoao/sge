<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends BaseModel
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = ['name', 'type_education_id', 'year'];

    protected $casts = [
        'id' => 'string',
    ];

    public function typeEducation()
    {
        return $this->belongsTo(TypeEducation::class);
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class, 'level_disciplines');
    }

    public function schoolLevelDisciplines()
    {
        return $this->hasMany(SchoolLevelDiscipline::class);
    }

    public function schoolLevels()
    {
        return $this->hasMany(SchoolLevel::class);
    }

    public function levelCourseDisciplines()
    {
        return $this->hasMany(LevelCourseDiscipline::class);
    }
}
