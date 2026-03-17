<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckinRequest;
use App\Http\Resources\ParticipantResource;
use App\Services\CheckinService;
use Illuminate\Http\JsonResponse;

class CheckinController extends Controller
{
    public function __construct(private readonly CheckinService $checkinService) {}

    public function __invoke(CheckinRequest $request): JsonResponse
    {
        $isQrPayload = $request->filled('qr_code_payload');
        $input = $isQrPayload ? $request->qr_code_payload : $request->ticket_code;

        $result = $this->checkinService->performCheckin(
            $input,
            $request->user(),
            $isQrPayload,
            $request->device,
        );

        return match ($result['status']) {
            'valid' => response()->json([
                'status' => 'valid',
                'participant' => $result['participant']
                    ? new ParticipantResource($result['participant'])
                    : null,
            ]),
            'already_used' => response()->json([
                'status' => 'already_used',
                'checked_in_at' => $result['checked_in_at'],
            ], 409),
            default => response()->json([
                'status' => 'invalid',
            ], 404),
        };
    }
}
