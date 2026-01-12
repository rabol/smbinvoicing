<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiController extends Controller
{
    public function sendResponse(string|array|JsonResource $data = '', string $message = 'ok', int $code = 200): JsonResponse
    {
        $response = [
            'data' => $data,
        ];

        return response()
            ->json($response, 200);
    }

    public function sendError(string|array $error, int $code = 404): JsonResponse
    {
        if (is_array($error)) {
            $errorResult = [];
            foreach ($error as $key => $value) {
                $errorResult[$key] = $value;
            }

            return response()->json([
                'errors' => [
                    'status' => $code,
                    'detail' => [
                        $errorResult,
                    ],
                ],
            ], $code);
        }

        return response()->json(
            [
                'errors' => [
                    'status' => $code,
                    'title' => $error,
                ],
            ],
            $code
        );
    }
}
