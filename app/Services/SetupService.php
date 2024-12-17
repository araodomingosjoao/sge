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
            'description' => 'Configure as informações básicas da sua instituição',
            'order' => 1,
            'icon' => 'ri-building-line',
            'required' => true
        ],
        'academic_periods' => [
            'name' => 'Períodos Acadêmicos',
            'description' => 'Defina os períodos letivos e trimestres',
            'order' => 2,
            'icon' => 'ri-calendar-line',
            'required' => true
        ],
        'courses' => [
            'name' => 'Cursos',
            'description' => 'Configure os cursos oferecidos',
            'order' => 3,
            'icon' => 'ri-book-open-line',
            'required' => false,
            'condition' => [
                'field' => 'type_education_id',
                'value' => 4
            ]
        ],
        'subjects' => [
            'name' => 'Disciplinas',
            'description' => 'Cadastre as disciplinas',
            'order' => 4,
            'icon' => 'ri-book-mark-line',
            'required' => true
        ],
        'classes' => [
            'name' => 'Turmas',
            'description' => 'Configure as turmas da escola',
            'order' => 5,
            'icon' => 'ri-group-line',
            'required' => true
        ],
        'departments' => [
            'name' => 'Departamentos',
            'description' => 'Organize a estrutura departamental',
            'order' => 6,
            'icon' => 'ri-git-branch-line',
            'required' => false
        ],
        'staff_roles' => [
            'name' => 'Cargos e Funções',
            'description' => 'Defina os cargos da equipe',
            'order' => 7,
            'icon' => 'ri-user-settings-line',
            'required' => false
        ],
        'final_review' => [
            'name' => 'Revisão Final',
            'description' => 'Revise todas as configurações',
            'order' => 8,
            'icon' => 'ri-checkbox-circle-line',
            'required' => true
        ]
    ];

    protected $school;

    public function __construct(School $school)
    {
        $this->school = $school;
    }

    public function getAvailableSteps(string $schoolId)
    {
        $school = School::find($schoolId);
        
        return collect(self::STEPS)
            ->filter(function ($step, $key) use ($school) {
                if (isset($step['condition'])) {
                    $field = $step['condition']['field'];
                    $value = $step['condition']['value'];
                    return $school->$field === $value;
                }
                return true;
            });
    }

    public function initializeSetupSteps(string $schoolId)
    {
        $availableSteps = $this->getAvailableSteps($schoolId);

        foreach ($availableSteps as $stepKey => $details) {
            SetupStep::firstOrCreate(
                [
                    'school_id' => $schoolId,
                    'step_key' => $stepKey
                ],
                [
                    'name' => $details['name'],
                    'data' => [
                        'order' => $details['order'],
                        'description' => $details['description'],
                        'icon' => $details['icon'],
                        'required' => $details['required'],
                        'key' => $stepKey
                    ],
                    'status' => 'pending'
                ]
            );
        }
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
            ->orderByRaw("CAST(JSON_EXTRACT(data, '$.order') AS SIGNED)")
            ->get();

        return [
            'total_steps' => count(self::STEPS),
            'completed_steps' => $steps->where('completed', true)->count(),
            'progress_percentage' => $steps->count() > 0 
                ? round(($steps->where('completed', true)->count() / $steps->count()) * 100)
                : 0,
            'current_step' => $this->getCurrentStep($schoolId)?->step_key,
            'steps' => $steps->map(function ($step) {
                $stepData = $step->data;
                return [
                    'key' => $step->step_key,
                    'name' => $step->name,
                    'description' => $stepData['description'] ?? null,
                    'icon' => $stepData['icon'] ?? null,
                    'order' => $stepData['order'] ?? null,
                    'required' => $stepData['required'] ?? false,
                    'status' => $step->status,
                    'completed' => $step->completed,
                    'completed_at' => $step->completed_at,
                    'can_skip' => !($stepData['required'] ?? false),
                    'submitted_data' => $stepData['submitted_data'] ?? null
                ];
            })
        ];
    }

    public function markStepAsCompleted(string $schoolId, string $stepKey, array $stepData = [])
    {
        $step = SetupStep::where('school_id', $schoolId)
            ->where('step_key', $stepKey)
            ->first();

        if (!$step) {
            throw new \Exception('Step not found');
        }

        $step->update([
            'completed' => true,
            'status' => 'completed',
            'completed_at' => Carbon::now(),
            'data' => array_merge($step->data ?? [], ['submitted_data' => $stepData])
        ]);

        $this->updateNextStepStatus($schoolId, $step);

        return $step;
    }

    protected function updateNextStepStatus(string $schoolId, SetupStep $currentStep)
    {
        $nextStep = SetupStep::where('school_id', $schoolId)
            ->where('completed', false)
            ->whereRaw("JSON_EXTRACT(data, '$.order') > ?", [$currentStep->data['order']])
            ->orderByRaw("JSON_EXTRACT(data, '$.order')")
            ->first();

        if ($nextStep) {
            $nextStep->update(['status' => 'in_progress']);
        }
    }

    public function skipStep(string $schoolId, string $stepKey)
    {
        $stepConfig = self::STEPS[$stepKey] ?? null;
        
        if (!$stepConfig || $stepConfig['required']) {
            throw new \Exception('This step cannot be skipped');
        }

        return $this->markStepAsCompleted($schoolId, $stepKey, ['skipped' => true]);
    }

    public function isSetupComplete(string $schoolId)
    {
        $steps = SetupStep::where('school_id', $schoolId)
            ->where('completed', false)
            ->whereRaw("JSON_EXTRACT(data, '$.required') = true")
            ->count();

        return $steps === 0;
    }
}
