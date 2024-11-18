<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolCalendar extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'school_id',
        'trimester_id',
        'event_name',
        'description',
        'event_type',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'id' => 'string',
        'school_id' => 'string',
        'trimester_id' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
        'event_type' => 'string'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function trimester()
    {
        return $this->belongsTo(Trimester::class);
    }
}