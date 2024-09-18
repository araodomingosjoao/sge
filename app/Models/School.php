<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type_education_id',
        'name',
        'logo',
        'address'
    ];

    protected $casts = [
        'id' => 'string',
        'type_education_id' => 'string',
    ];

    public function typeEducation()
    {
        return $this->belongsTo(TypeEducation::class, 'type_education_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'school_id');
    }
}
