<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discipline extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    protected $casts = [
        'id' => 'string',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_disciplines');
    }
}
