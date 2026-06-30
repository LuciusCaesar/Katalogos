<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBusinessObjectiveRequest;
use App\Http\Requests\Api\UpdateBusinessObjectiveRequest;
use App\Http\Resources\BusinessObjectiveResource;
use App\Models\BusinessObjective;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BusinessObjectiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return BusinessObjectiveResource::collection(BusinessObjective::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusinessObjectiveRequest $request): BusinessObjectiveResource
    {
        $businessObjective = BusinessObjective::create($request->validated());

        return new BusinessObjectiveResource($businessObjective);
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessObjective $businessObjective): BusinessObjectiveResource
    {
        return new BusinessObjectiveResource($businessObjective);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusinessObjectiveRequest $request, BusinessObjective $businessObjective): BusinessObjectiveResource
    {
        $businessObjective->update($request->validated());

        return new BusinessObjectiveResource($businessObjective);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessObjective $businessObjective): JsonResponse
    {
        $businessObjective->delete();

        return response()->json(null, 204);
    }
}
