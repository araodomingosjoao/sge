<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelDiscipline extends Model
{
    use HasFactory;

    protected $table = "level_discipline";
    protected $fillable = [
        'level_id',
        'discipline_id'
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
}
