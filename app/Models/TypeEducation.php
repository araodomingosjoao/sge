<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeEducation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'type_educations';
    protected $fillable = ['name'];

    protected $casts = [
        'id' => 'string',
    ];

    public function schools()
    {
        return $this->hasMany(School::class, 'type_education_id');
    }
}
