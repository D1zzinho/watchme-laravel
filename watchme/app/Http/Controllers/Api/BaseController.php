<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class BaseController extends Controller
{
    /**
     * Success response method.
     *
     * @param  $result
     * @param  $message
     * @return JsonResponse
     */
    public function sendResponse($result, $message): JsonResponse
    {
        $response = [
            'success'   => true,
            'data'      => $result,
            'message'   => $message,
            'timestamp' => Carbon::now()
        ];

        return response()->json($response);
    }


    /**
     * Returns error response.
     *
     * @param        $error
     * @param  array $errorMessages
     * @param  int   $code
     * @return JsonResponse
     */
    public function sendError($error, array $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'success'   => false,
            'message'   => $error,
            'timestamp' => Carbon::now()
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
