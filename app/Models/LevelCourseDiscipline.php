<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelCourseDiscipline extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'course_id',
        'discipline_id'
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
}