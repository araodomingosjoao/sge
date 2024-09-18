<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description'];

    protected $casts = [
        'id' => 'string',
    ];

    public function grades()
    {
        return $this->hasMany(Grade::class, 'test_type_id');
    }
}
