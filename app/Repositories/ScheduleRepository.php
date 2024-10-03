<?php

namespace App\Repositories;

use App\Models\Schedule;

class ScheduleRepository extends BaseRepository
{
    public function __construct(Schedule $schedule)
    {
        parent::__construct($schedule);
        $this->relationships = ['teacher', 'discipline', 'class', 'classroom'];
    }
}
