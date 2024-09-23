<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trimester extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'academic_year'];

    protected $casts = [
        'id' => 'string',
        'academic_year' => 'date',
    ];
}
