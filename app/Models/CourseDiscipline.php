<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseDiscipline extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['course_id', 'discipline_id'];

    protected $casts = [
        'id' => 'string',
        'course_id' => 'string',
        'discipline_id' => 'string',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'discipline_id');
    }
}
