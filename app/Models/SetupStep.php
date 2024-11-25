<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetupStep extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['school_id', 'step_key', 'name', 'completed', 'completed_at', 'data'];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'data' => 'array'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
