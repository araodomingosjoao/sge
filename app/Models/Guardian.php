<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'student_id',
        'address',
        'kinship',
        'birth_date'
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'student_id' => 'string',
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
