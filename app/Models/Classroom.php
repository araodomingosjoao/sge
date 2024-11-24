<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classroom extends BaseModel
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'school_id', 
        'name', 
        'capacity'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
