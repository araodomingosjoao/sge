<?php

namespace App\Repositories;

use App\Models\Level;
use App\Models\SchoolLevel;

class LevelRepository extends BaseRepository
{
    public function __construct(SchoolLevel $level)
    {
        parent::__construct($level);
    }
}
