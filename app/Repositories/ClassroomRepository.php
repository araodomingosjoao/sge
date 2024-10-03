<?php

namespace App\Repositories;

use App\Models\Classroom;

class ClassroomRepository extends BaseRepository
{
    public function __construct(Classroom $classroom)
    {
        parent::__construct($classroom);
    }
}
