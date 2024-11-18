<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trimester extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'school_id',
        'name',
        'trimester_number',
        'academic_year',
    ];

    protected $casts = [
        'id' => 'string',
        'school_id' => 'string',
        'trimester_number' => 'integer',
        'academic_year' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function calendar()
    {
        return $this->hasOne(SchoolCalendar::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}