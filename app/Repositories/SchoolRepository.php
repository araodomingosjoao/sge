<?php

namespace App\Repositories;

use App\Models\School;

class SchoolRepository extends BaseRepository
{
    public function __construct(School $school)
    {
        parent::__construct($school);
        $this->relationships = ['typeEducation'];
    }
}
