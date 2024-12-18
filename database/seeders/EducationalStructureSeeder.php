<?php
namespace Database\Seeders;

use App\Enums\TypeEducationEnum;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseDiscipline;
use App\Models\Discipline;
use App\Models\Level;
use App\Models\LevelCourseDiscipline;
use App\Models\LevelDiscipline;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationalStructureSeeder extends Seeder
{
    private array $fundamentalStructure = [
        'disciplines' => [
            'Matemática' => [1, 2, 3, 4, 5, 6, 7, 8, 9],
            'Português' => [1, 2, 3, 4, 5, 6, 7, 8, 9],
            'História' => [5, 6, 7, 8, 9],
            'Ciências' => [5, 6, 7, 8, 9],
        ]
    ];

    private array $highSchoolStructure = [
        'categories' => [
            'Saúde' => [
                'courses' => [
                    'Enfermagem' => [
                        'disciplines' => [
                            'Anatomia' => [10, 11],
                            'Enfermagem Básica' => [10],
                            'Farmacologia' => [11],
                        ]
                    ]
                ]
            ],
            'Técnico' => [
                'courses' => [
                    'Informática' => [
                        'disciplines' => [
                            'Programação' => [12, 13],
                            'Banco de Dados' => [12],
                            'Redes de Computadores' => [13],
                        ]
                    ]
                ]
            ],
            'Humanas' => [
                'courses' => [
                    'Direito' => [
                        'disciplines' => [
                            'Introdução ao Direito' => [11, 12],
                            'Direito Constitucional' => [11],
                            'Ética Jurídica' => [12],
                        ]
                    ]
                ]
            ]
        ]
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedLevels();
            $this->seedFundamentalStructure();
            $this->seedHighSchoolStructure();
        });
    }

    private function seedLevels(): void
    {
        // Anos Iniciais (1-6)
        foreach (range(1, 6) as $year) {
            Level::create([
                'type_education_id' => TypeEducationEnum::FUNDAMENTAL_INITIAL,
                'name' => "{$year}º Ano",
                'year' => $year
            ]);
        }

        // Anos Finais (7-9)
        foreach (range(7, 9) as $year) {
            Level::create([
                'type_education_id' => TypeEducationEnum::FUNDAMENTAL_FINAL,
                'name' => "{$year}º Ano",
                'year' => $year
            ]);
        }

        // Ensino Médio (10-13)
        foreach (range(10, 13) as $year) {
            Level::create([
                'type_education_id' => TypeEducationEnum::HIGH_SCHOOL,
                'name' => "{$year}º Ano",
                'year' => $year
            ]);
        }
    }

    private function seedFundamentalStructure(): void
    {
        foreach ($this->fundamentalStructure['disciplines'] as $disciplineName => $years) {
            $discipline = Discipline::create(['name' => $disciplineName]);
            
            foreach ($years as $year) {
                $level = Level::where('year', $year)->first();
                
                if ($level) {
                    LevelDiscipline::create([
                        'level_id' => $level->id,
                        'discipline_id' => $discipline->id
                    ]);
                }
            }
        }
    }

    private function seedHighSchoolStructure(): void
    {
        foreach ($this->highSchoolStructure['categories'] as $categoryName => $categoryData) {
            $category = Category::create(['name' => $categoryName]);

            foreach ($categoryData['courses'] as $courseName => $courseData) {
                $course = Course::create([
                    'name' => $courseName,
                    'category_id' => $category->id
                ]);

                foreach ($courseData['disciplines'] as $disciplineName => $years) {
                    $discipline = Discipline::create(['name' => $disciplineName]);
                    
                    // Associa disciplina ao curso
                    CourseDiscipline::create([
                        'course_id' => $course->id,
                        'discipline_id' => $discipline->id
                    ]);

                    // Associa disciplina aos níveis específicos
                    foreach ($years as $year) {
                        $level = Level::where('year', $year)
                            ->where('type_education_id', TypeEducationEnum::HIGH_SCHOOL)
                            ->first();

                        if ($level) {
                            LevelDiscipline::create([
                                'level_id' => $level->id,
                                'discipline_id' => $discipline->id
                            ]);

                            // Associação entre nível, curso e disciplina
                            LevelCourseDiscipline::create([
                                'level_id' => $level->id,
                                'course_id' => $course->id,
                                'discipline_id' => $discipline->id
                            ]);
                        }
                    }
                }
            }
        }
    }
}
