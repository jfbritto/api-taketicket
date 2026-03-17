<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPaymentWebhookJob;
use App\Services\AsaasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function asaas(Request $request, AsaasService $asaasService): JsonResponse
    {
        if (!$asaasService->verifyWebhookToken($request)) {
            abort(401);
        }

        ProcessPaymentWebhookJob::dispatch($request->all());

        return response()->json(['received' => true]);
    }
}
