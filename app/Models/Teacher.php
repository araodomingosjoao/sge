<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = ['user_id', 'address', 'birth_date'];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function photo()
    {
        return $this->morphOne(Photo::class, 'imageable');
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class, 'teacher_discipline_class')
                    ->withPivot('class_id')
                    ->withTimestamps();
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'teacher_discipline_class')
                    ->withPivot('discipline_id')
                    ->withTimestamps();
    }
}
