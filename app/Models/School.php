<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'type_education_id',
        'school_name',
        'logo_path',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'website',
        'founded_year',
        'registration_number',
        'status',
        'category_id'
    ];

    protected $casts = [
        'id' => 'string',
        'type_education_id' => 'integer',
        'category_id' => 'string',
        'founded_year' => 'integer',
        'status' => 'integer',
    ];

    public static function getFileFields(): array
    {
        return [
                'logo_path' => [
                    'folder' => 'logos',
                    'disk' => 'public',
                    'fileName' => fn ($file) => Str::random(40) . '.' . $file->getClientOriginalExtension(),
                ]
            ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function typeEducation()
    {
        return $this->belongsTo(TypeEducation::class, 'type_education_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'school_id');
    }

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class, 'school_id');
    }

    public function courseDisciplines()
    {
        return $this->hasMany(SchoolCourseDiscipline::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function calendars()
    {
        return $this->hasMany(SchoolCalendar::class);
    }

    public function trimesters()
    {
        return $this->hasMany(Trimester::class);
    }

    public function courses()
    {
        return $this->hasMany(SchoolCourse::class);
    }

    public function levels()
    {
        return $this->hasMany(SchoolLevel::class);
    }

    public function disciplines()
    {
        return $this->hasMany(SchoolDiscipline::class);
    }

    public function levelDisciplines()
    {
        return $this->hasMany(SchoolLevelDiscipline::class);
    }

    public function getDisciplinesByLevel($levelId)
    {
        return $this->levelDisciplines()
            ->where('level_id', $levelId)
            ->with(['discipline', 'course'])
            ->get();
    }

    public function getDisciplinesByCourse($courseId)
    {
        return $this->levelDisciplines()
            ->where('course_id', $courseId)
            ->with(['discipline', 'level'])
            ->get();
    }
}
