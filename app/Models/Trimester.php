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
        'academic_year_id', 
        'name', 
        'start_date', 
        'end_date'
    ];

    protected $dates = ['start_date', 'end_date'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
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