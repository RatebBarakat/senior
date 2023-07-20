<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseApi
{
    protected function successResponse(array $data, string $message = ""): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data
        ];

        if (!is_null($message)) {
            $response['message'] = $message;
        }

        return response()->json($response,200);
    }
    protected function unUnauthorized(string $message = "unauthorized"):JsonResponse
    {
        return response()->json(['message' => $message],401);
    }
    protected function validationErrors($errors):JsonResponse
    {
        return response()->json($errors,412);
    }
    protected function responseError(string $message,int $status = 400):JsonResponse
    {
        return response()->json(['message' => $message],$status);
    }
}
