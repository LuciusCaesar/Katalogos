<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBusinessEventRequest;
use App\Http\Requests\Api\UpdateBusinessEventRequest;
use App\Http\Resources\BusinessEventResource;
use App\Models\BusinessEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BusinessEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return BusinessEventResource::collection(
            BusinessEvent::with(['businessAssets', 'businessProcesses'])->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusinessEventRequest $request): BusinessEventResource
    {
        $businessEvent = BusinessEvent::create($request->validated());

        if ($request->has('business_asset_ids')) {
            $businessEvent->businessAssets()->sync($request->input('business_asset_ids'));
        }

        if ($request->has('business_process_ids')) {
            $businessEvent->businessProcesses()->sync($request->input('business_process_ids'));
        }

        return new BusinessEventResource($businessEvent->load(['businessAssets', 'businessProcesses']));
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessEvent $businessEvent): BusinessEventResource
    {
        return new BusinessEventResource($businessEvent->load(['businessAssets', 'businessProcesses']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusinessEventRequest $request, BusinessEvent $businessEvent): BusinessEventResource
    {
        $businessEvent->update($request->validated());

        if ($request->has('business_asset_ids')) {
            $businessEvent->businessAssets()->sync($request->input('business_asset_ids'));
        }

        if ($request->has('business_process_ids')) {
            $businessEvent->businessProcesses()->sync($request->input('business_process_ids'));
        }

        return new BusinessEventResource($businessEvent->load(['businessAssets', 'businessProcesses']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessEvent $businessEvent): JsonResponse
    {
        $businessEvent->delete();

        return response()->json(null, 204);
    }
}
