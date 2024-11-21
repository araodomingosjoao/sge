<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['name'];

    protected $casts = [
        'id' => 'string',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function classes()
    {
        return $this->hasMany(ClassModel::class, 'course_id');
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class, 'course_disciplines');
    }
}
