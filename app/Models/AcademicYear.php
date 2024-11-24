<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'school_id', 
        'year', 
        'start_date', 
        'end_date', 
        'status'
    ];

    protected $dates = ['start_date', 'end_date'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function trimesters()
    {
        return $this->hasMany(Trimester::class);
    }
}
