<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolCalendar extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['trimester_id', 'start_date', 'end_date'];

    protected $casts = [
        'id' => 'string',
        'trimester_id' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function trimester()
    {
        return $this->belongsTo(Trimester::class, 'trimester_id');
    }
}
