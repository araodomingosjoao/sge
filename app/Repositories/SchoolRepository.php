<?php

namespace App\Repositories;

use App\Models\School;

class SchoolRepository extends BaseRepository
{
    public function __construct(School $user)
    {
        parent::__construct($user);
    }
}
