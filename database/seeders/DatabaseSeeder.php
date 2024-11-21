<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Level;
use App\Models\TypeEducation;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TypeEducationSeeder::class,
            RolesAndPermissionsSeeder::class,
            DefaultLevelsSeeder::class,
        ]);

        // Recuperar os tipos de educação
        $fundamental = TypeEducation::where('name', 'Ensino Fundamental')->first();
        $medio = TypeEducation::where('name', 'Ensino Médio')->first();

        // Definir disciplinas gerais do Ensino Fundamental com os anos correspondentes
        $generalDisciplines = [
            'Matemática' => [1, 2, 3, 4, 5, 6, 7, 8, 9],
            'Português' => [1, 2, 3, 4, 5, 6, 7, 8, 9],
            'História' => [5, 6, 7, 8, 9],
            'Ciências' => [5, 6, 7, 8, 9],
        ];

        foreach ($generalDisciplines as $disciplineName => $levelYears) {
            $discipline = Discipline::firstOrCreate(['name' => $disciplineName]);

            foreach ($levelYears as $year) {
                $level = Level::where('year', $year)
                    ->first();

                if ($level) {
                    $level->disciplines()->syncWithoutDetaching($discipline);
                } else {
                    $this->command->error("Nível $year para a disciplina $disciplineName não encontrado.");
                }
            }
        }

        // Criar disciplinas específicas para o Ensino Médio
        $disciplineLevelAssociations = [
            'Enfermagem' => [
                'Anatomia' => [10, 11],
                'Enfermagem Básica' => [10],
                'Farmacologia' => [11],
            ],
            'Informática' => [
                'Programação' => [12, 13],
                'Banco de Dados' => [12],
                'Redes de Computadores' => [13],
            ],
            'Direito' => [
                'Introdução ao Direito' => [11, 12],
                'Direito Constitucional' => [11],
                'Ética Jurídica' => [12],
            ],
        ];

        // Criar categorias e cursos
        $categories = [
            'Saúde' => ['Enfermagem'],
            'Técnico' => ['Informática'],
            'Humanas' => ['Direito'],
        ];

        foreach ($categories as $categoryName => $courses) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            foreach ($courses as $courseName) {
                $course = Course::firstOrCreate([
                    'category_id' => $category->id,
                    'name' => $courseName,
                ]);

                if (isset($disciplineLevelAssociations[$courseName])) {
                    foreach ($disciplineLevelAssociations[$courseName] as $disciplineName => $levelYears) {
                        $discipline = Discipline::firstOrCreate(['name' => $disciplineName]);

                        foreach ($levelYears as $year) {
                            $level = Level::where('year', $year)
                                ->where('type_education_id', $medio->id)
                                ->first();

                            if ($level) {
                                $level->disciplines()->syncWithoutDetaching($discipline);
                            } else {
                                $this->command->error("Nível $year para o curso $courseName não encontrado.");
                            }
                        }

                        $course->disciplines()->syncWithoutDetaching($discipline);
                    }
                }
            }
        }
    }
}
