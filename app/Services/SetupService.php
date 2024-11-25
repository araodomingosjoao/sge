<?php

namespace App\Services;

use App\Models\SetupStep;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SetupService
{
    const STEPS = [
        'school_info' => [
            'name' => 'Informações da Escola',
            'order' => 1,
            'required_fields' => ['school_name', 'type_education_id', 'phone']
        ],
        'courses' => [
            'name' => 'Definir Cursos e Disciplinas',
            'order' => 2,
            'required_fields' => []
        ],
        'classes' => [
            'name' => 'Criação de Turmas',
            'order' => 3,
            'required_fields' => []
        ],
        'departments' => [
            'name' => 'Departamentos',
            'order' => 4,
            'required_fields' => []
        ],
        'final_setup' => [
            'name' => 'Configurações Finais',
            'order' => 5,
            'required_fields' => ['configuration_completed']
        ]
    ];

    protected $school;

    public function __construct(School $school)
    {
        $this->school = $school;
    }

    public function initializeSetupSteps(string $schoolId)
    {
        foreach (self::STEPS as $key => $details) {
            SetupStep::firstOrCreate(
                [
                    'school_id' => $schoolId,
                    'step_key' => $key
                ],
                [
                    'name' => $details['name'],
                    'data' => [
                        'order' => $details['order'],
                        'required_fields' => $details['required_fields']
                    ]
                ]
            );
        }
    }

    public function isFirstLogin(string $schoolId)
    {
        $cacheKey = "setup_initialized_school_{$schoolId}";

        if (!Cache::has($cacheKey)) {
            $isFirst = SetupStep::where('school_id', $schoolId)
                ->where('completed', true)
                ->count() === 0;
            Cache::put($cacheKey, true, now()->addDay());
            return $isFirst;
        }

        return false;
    }

    public function getCurrentStep(string $schoolId)
    {
        return SetupStep::where('school_id', $schoolId)
            ->where('completed', false)
            ->orderByRaw("CASE 
                WHEN JSON_EXTRACT(data, '$.order') IS NULL THEN 99999 
                ELSE CAST(JSON_EXTRACT(data, '$.order') AS SIGNED) 
            END")
            ->first();
    }

    public function getProgress(string $schoolId)
    {
        $steps = SetupStep::where('school_id', $schoolId)
            ->orderByRaw("CASE 
                WHEN JSON_EXTRACT(data, '$.order') IS NULL THEN 99999 
                ELSE CAST(JSON_EXTRACT(data, '$.order') AS SIGNED) 
            END")
            ->get();

        return [
            'total_steps' => count(self::STEPS),
            'completed_steps' => $steps->where('completed', true)->count(),
            'current_step' => $this->getCurrentStep($schoolId)?->step_key,
            'steps' => $steps->map(function ($step) {
                return [
                    'key' => $step->step_key,
                    'name' => $step->name,
                    'completed' => $step->completed,
                    'completed_at' => $step->completed_at,
                    'required_fields' => self::STEPS[$step->step_key]['required_fields'] ?? [],
                    'order' => self::STEPS[$step->step_key]['order'] ?? null
                ];
            })
        ];
    }

    public function markStepAsCompleted(string $schoolId, string $stepKey, array $stepData)
    {
        $step = SetupStep::where('school_id', $schoolId)
            ->where('step_key', $stepKey)
            ->first();

        if (!$step) {
            throw new \Exception('Step not found');
        }

        // Validar campos obrigatórios
        $requiredFields = self::STEPS[$stepKey]['required_fields'] ?? [];
        $missingFields = array_diff($requiredFields, array_keys($stepData));

        if (!empty($missingFields)) {
            throw new \Exception('Missing required fields: ' . implode(', ', $missingFields));
        }

        $step->update([
            'completed' => true,
            'completed_at' => Carbon::now(),
            'data' => array_merge($step->data ?? [], ['submitted_data' => $stepData])
        ]);

        return $step;
    }

    public function isSetupComplete(string $schoolId)
    {
        return SetupStep::where('school_id', $schoolId)
            ->where('completed', false)
            ->count() === 0;
    }
}
