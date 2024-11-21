<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name'
    ];

    public function schools()
    {
        return $this->hasMany(School::class, 'category_id', 'id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id', 'id');
    }
}
