<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomFieldRequest;
use App\Http\Requests\UpdateCustomFieldRequest;
use App\Models\CustomField;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

class CustomFieldController extends Controller
{
    public function index(Event $event): JsonResponse
    {
        $this->authorize('manage', $event);

        return response()->json($event->customFields);
    }

    public function store(StoreCustomFieldRequest $request, Event $event): JsonResponse
    {
        $this->authorize('manage', $event);
        $field = $event->customFields()->create($request->validated());

        return response()->json($field, 201);
    }

    public function update(UpdateCustomFieldRequest $request, Event $event, CustomField $customField): JsonResponse
    {
        $this->authorize('manage', $event);
        abort_unless($customField->event_id === $event->id, 404);

        $customField->update($request->validated());

        return response()->json($customField->fresh());
    }

    public function destroy(Event $event, CustomField $customField): JsonResponse
    {
        $this->authorize('manage', $event);
        abort_unless($customField->event_id === $event->id, 404);
        abort_if($customField->hasValues(), 422, 'Cannot delete field with existing values');

        $customField->delete();

        return response()->json(null, 204);
    }
}
