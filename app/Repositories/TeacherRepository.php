<?php

namespace App\Repositories;

use App\Models\Teacher;

class TeacherRepository extends BaseRepository
{
    public function __construct(Teacher $teacher)
    {
        parent::__construct($teacher);
    }
}
