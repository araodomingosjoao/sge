<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolLevelDiscipline extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "school_level_discipline";
    protected $fillable = ['school_id', 'level_id', 'discipline_id'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
}

