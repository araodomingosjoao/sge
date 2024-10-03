<?php

namespace App\Repositories;

use App\Models\TeacherDisciplineClass;

class AllocationRepository extends BaseRepository
{
    public function __construct(TeacherDisciplineClass $teacherDisciplineClass)
    {
        parent::__construct($teacherDisciplineClass);
        $this->relationships = ['teacher', 'discipline', 'class'];
    }
}
