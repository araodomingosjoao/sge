<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'class_id',
        'photo_id',
        'case_number',
        'address',
        'observation',
        'birth_date',
        'status'
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'class_id' => 'string',
        'photo_id' => 'string',
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function photo()
    {
        return $this->morphOne(Photo::class, 'imageable');
    }
}
