<?php

namespace App\Http\Controllers\API;

use App\DTO\CreateOrganizerDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganizerRequest;
use App\Http\Requests\UpdateOrganizerRequest;
use App\Http\Resources\OrganizerResource;
use App\Services\OrganizerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    public function __construct(private readonly OrganizerService $organizerService) {}

    public function store(StoreOrganizerRequest $request): JsonResponse
    {
        if ($request->user()->organizer) {
            return response()->json(['message' => 'User already has an organizer profile'], 422);
        }

        $dto = CreateOrganizerDTO::fromRequest($request->validated());
        $organizer = $this->organizerService->createOrganizer($request->user(), $dto);

        return response()->json(new OrganizerResource($organizer), 201);
    }

    public function me(Request $request): JsonResponse
    {
        $organizer = $request->user()->organizer;
        abort_unless($organizer, 404, 'No organizer profile found');

        return response()->json(new OrganizerResource($organizer));
    }

    public function update(UpdateOrganizerRequest $request): JsonResponse
    {
        $organizer = $request->user()->organizer;
        abort_unless($organizer, 404, 'No organizer profile found');

        $dto = CreateOrganizerDTO::fromRequest($request->validated());
        $organizer = $this->organizerService->updateOrganizer($organizer, $dto);

        return response()->json(new OrganizerResource($organizer));
    }
}
