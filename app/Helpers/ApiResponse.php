<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data, $message = 'Operation successful', $code = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error($message = 'Operation failed', $code = 400)
    {
        return response()->json([
            'message' => $message,
        ], $code);
    }

    public static function paginated($results, $resource, $code = 200)
    {
        return response()->json([
            'data' => $resource::collection($results->items()),
            'meta' => [
                'current_page' => $results->currentPage(),
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'last_page' => $results->lastPage(),
                'next_page_url' => $results->nextPageUrl(),
                'prev_page_url' => $results->previousPageUrl(),
            ],
        ], $code);
    }
}
