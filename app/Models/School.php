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
    ];

    protected $casts = [
        'id' => 'string',
        'type_education_id' => 'string',
    ];

    protected $fileFields;

    /**
     * Get the file fields for the model.
     *
     * @return array
     */
    public static function getFileFields(): array
    {
        return [
                'logo' => [
                    'folder' => 'logos',
                    'disk' => 'public',
                    'fileName' => fn ($file) => Str::random(40) . '.' . $file->getClientOriginalExtension(),
                ]
            ];
    }

    public function typeEducation()
    {
        return $this->belongsTo(TypeEducation::class, 'type_education_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'school_id');
    }
}
