<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Academic Year",
 *     description="API Endpoints for Academic Year"
 * )
 */
class AcademicYearController extends Controller
{
    /**
     * @OA\Get(
     *     path="/academic-years",
     *     summary="List all academic years and their trimesters",
     *     tags={"Academic Year"},
     *     security={{ "sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="List of academic years and their trimesters",
     *         @OA\JsonContent(
     *             
     *         )
     *     )
     * )
     */
    public function showAcademicYearWithTrimesters()
    {
        $school = Auth::user()->school;

        if (!$school) {
            return ApiResponse::error('Nenhuma escola associada ao usuário logado.', 404);
        }

        $academicYears = $school->academicYears()
            ->with('trimesters')
            ->get();

        return ApiResponse::success($academicYears);
    }

    /**
     * @OA\Put(
     *     path="/academic-years/{academicYear}/trimesters",
     *     summary="Update multiple trimesters for an academic year",
     *     tags={"Academic Year"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="academicYear",
     *         in="path",
     *         description="ID of the academic year",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="trimesters",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="9d8f8191-2362-468a-a362-c3c0b9f9efb5"),
     *                     @OA\Property(property="start_date", type="string", format="date", example="2024-01-01"),
     *                     @OA\Property(property="end_date", type="string", format="date", example="2024-04-30")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trimesters updated successfully",
     *         @OA\JsonContent(
     *             
     *         )
     *     )
     * )
     */
    public function updateTrimesters(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'trimesters' => 'required|array',
            'trimesters.*.id' => 'required|exists:trimesters,id',
            'trimesters.*.start_date' => 'required|date',
            'trimesters.*.end_date' => 'required|date|after:trimesters.*.start_date',
        ]);

        foreach ($validated['trimesters'] as $trimesterData) {
            $trimester = $academicYear->trimesters()->find($trimesterData['id']);
            if ($trimester) {
                $trimester->update([
                    'start_date' => $trimesterData['start_date'],
                    'end_date' => $trimesterData['end_date'],
                ]);
            }
        }

        $updatedTrimesters = $academicYear->trimesters;

        return ApiResponse::success($updatedTrimesters);
    }

    /**
     * @OA\Put(
     *     path="/academic-years/{academicYear}",
     *     summary="Update an academic year",
     *     tags={"Academic Year"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="academicYear",
     *         in="path",
     *         description="ID of the academic year",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-12-31"),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Academic year updated successfully",
     *         @OA\JsonContent(
     *             
     *         )
     *     )
     * )
     */
    public function updateAcademicYear(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $academicYear->update($validated);

        return ApiResponse::success([
            'data' => $academicYear->fresh(),
            'message' => 'Academic year updated successfully.',
        ]);
    }
}
