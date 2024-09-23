<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'path', 
        'provider',
        'imageable_id',
        'imageable_type'
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
