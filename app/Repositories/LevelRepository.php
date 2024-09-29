<?php

namespace App\Repositories;

use App\Models\Level;

class LevelRepository extends BaseRepository
{
    public function __construct(Level $level)
    {
        parent::__construct($level);
    }
}
