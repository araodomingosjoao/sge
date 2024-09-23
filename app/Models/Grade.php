<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['student_id', 'discipline_id', 'trimestre_id', 'type_test_id', 'grade'];

    protected $casts = [
        'id' => 'string',
        'student_id' => 'string',
        'discipline_id' => 'string',
        'trimestre_id' => 'string',
        'type_test_id' => 'string',
        'grade' => 'float',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'discipline_id');
    }

    public function trimestre()
    {
        return $this->belongsTo(Trimester::class, 'trimestre_id');
    }

    public function testType()
    {
        return $this->belongsTo(TestType::class, 'test_type_id');
    }
}
