<?php
namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Course",
 *     title="Course",
 *     description="Modelo de Curso",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="category_id", type="string", format="uuid")
 * )
 */
class CourseSchema {}