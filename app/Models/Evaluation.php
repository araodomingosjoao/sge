<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'school_id',
        'class_id',
        'discipline_id',
        'trimester_id',
        'test_type_id',
        'title',
        'evaluation_date',
        'max_grade',
        'description'
    ];

    protected $casts = [
        'id' => 'string',
        'school_id' => 'string',
        'class_id' => 'string',
        'discipline_id' => 'string',
        'trimester_id' => 'string',
        'test_type_id' => 'integer',
        'evaluation_date' => 'date',
        'max_grade' => 'decimal:2'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }

    public function trimester()
    {
        return $this->belongsTo(Trimester::class);
    }

    public function testType()
    {
        return $this->belongsTo(TestType::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
