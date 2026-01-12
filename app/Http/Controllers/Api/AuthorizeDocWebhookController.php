<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthorizeDocWebhookController extends ApiController
{
    public function handle(Request $request): JsonResponse
    {
        $data = $request->all();
        Log::info($data);

        return $this->sendResponse();
    }
}
