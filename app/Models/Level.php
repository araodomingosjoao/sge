<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends BaseModel
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = ['name', 'type_education_id', 'year'];

    protected $casts = [
        'id' => 'string',
    ];

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'level_id');
    }

    public function typeEducation()
    {
        return $this->belongsTo(TypeEducation::class);
    }
}
