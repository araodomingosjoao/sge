<?php

namespace App\Repositories;

use App\Models\Discipline;

class DisciplineRepository extends BaseRepository
{
    public function __construct(Discipline $discipline)
    {
        parent::__construct($discipline);
    }
}
