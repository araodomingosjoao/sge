<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolDiscipline extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = ['school_id', 'discipline_id'];

    protected $casts = [
        'id' => 'string',
        'school_id' => 'string',
        'discipline_id' => 'string'
    ];
}
