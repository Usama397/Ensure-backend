<?php

namespace App\Traits;

trait ResponseAPI
{
    /**
     * Core of response
     *
     * @param string $message
     * @param array|object $data
     * @param integer $statusCode
     * @param boolean $isSuccess
     */
    public function coreResponse(string $message, $data = null, int $responseCode, int $statusCode, bool $isSuccess = true): \Illuminate\Http\JsonResponse
    {
        // Check the params
        if (!$message) return response()->json(['message' => 'Message is required'], 500);

        // Send the response
        if ($isSuccess) {
            if (!$data->isEmpty()) {
                return response()->json([
                    'status' => $responseCode,
                    'message' => $message,
                    'error' => false,
                    'results' => $data
                ], $statusCode);
            } else {
                return response()->json([
                    'status' => 204,
                    'message' => $message,
                    'error' => false,
                    'results' => $data
                ], $statusCode);
            }
        } else {
            return response()->json([
                'message' => $message,
                'error' => true,
                'status' => $statusCode,
            ], $statusCode);
        }
    }

    /**
     * Send any success response
     *
     * @param string $message
     * @param array|object $data
     * @param integer $statusCode
     */
    public function success(string $message, $data, int $responseCode, int $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return $this->coreResponse($message, $data, $responseCode, $statusCode);
    }

    /**
     * Send any error response
     *
     * @param string $message
     * @param integer $statusCode
     */
    public function error(string $message, int $statusCode = 500): \Illuminate\Http\JsonResponse
    {
        return $this->coreResponse($message, null, 500, $statusCode, false);
    }
}
