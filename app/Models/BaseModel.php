<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {
    
    public static function getFileFields(): array
    {
        return [];
    }
}