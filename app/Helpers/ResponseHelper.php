<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Generate a padronized JSON response with a little tip to help the user
     *
     * @param  string  $message  main message of the response
     * @param  array  $otherThings  optional data to include in the JSON
     * @return JsonResponse HTTP response in JSON format
     */
    public static function withTip(string $message, array $otherThings = [], $httpStatus = 200): JsonResponse
    {
        return response()->json(array_merge([
            'tip' => 'the accepted format is 0000-00-00 (year-month-day)',
            'message' => $message,
        ], $otherThings), $httpStatus);
    }
}
