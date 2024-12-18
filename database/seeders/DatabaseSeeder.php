<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Level;
use App\Models\SchoolCourseDiscipline;
use App\Models\SchoolDiscipline;
use App\Models\SchoolLevelDiscipline;
use App\Models\TypeEducation;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \Log::info("Iniciando DatabaseSeeder");
        
        DB::transaction(function () {
            $this->call([
                TypeEducationSeeder::class,
                RolesAndPermissionsSeeder::class,
                EducationalStructureSeeder::class,
            ]);

            \Log::info("Seeders básicos concluídos");
            
            // Verificar os dados criados
            $disciplinesCount = SchoolDiscipline::count();
            $levelDisciplinesCount = SchoolLevelDiscipline::count();
            $courseDisciplinesCount = SchoolCourseDiscipline::count();
            
            \Log::info("Contagem após seeder:", [
                'school_disciplines' => $disciplinesCount,
                'school_level_disciplines' => $levelDisciplinesCount,
                'school_course_disciplines' => $courseDisciplinesCount
            ]);
        });
    }
}
