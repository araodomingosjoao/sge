<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'discipline_id',
        'trimester_id',
        'test_type_id',
        'evaluation_id',
        'grade',
        'academic_year',
        'observation',
        'status'
    ];

    protected $casts = [
        'id' => 'string',
        'student_id' => 'string',
        'class_id' => 'string',
        'discipline_id' => 'string',
        'trimester_id' => 'string',
        'evaluation_id' => 'string',
        'test_type_id' => 'integer',
        'grade' => 'decimal:2',
        'academic_year' => 'integer',
        'status' => 'string'
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

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
