<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDataInitiativeRequest;
use App\Http\Requests\Api\UpdateDataInitiativeRequest;
use App\Http\Resources\DataInitiativeResource;
use App\Models\DataInitiative;
use App\Services\DataInitiativeGovernanceProjection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DataInitiativeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return DataInitiativeResource::collection(DataInitiative::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDataInitiativeRequest $request): DataInitiativeResource
    {
        $dataInitiative = DataInitiative::create($request->validated());

        return new DataInitiativeResource($dataInitiative);
    }

    /**
     * Display the specified resource.
     */
    public function show(DataInitiative $dataInitiative): DataInitiativeResource
    {
        return new DataInitiativeResource($dataInitiative);
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateDataInitiativeRequest $request, DataInitiative $dataInitiative): DataInitiativeResource
    {
        $dataInitiative->update($request->validated());

        return new DataInitiativeResource($dataInitiative);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataInitiative $dataInitiative): JsonResponse
    {
        $dataInitiative->delete();

        return response()->json(null, 204);
    }

    /**
     * Get the governance score history of a data initiative, projected from
     * the business asset score fact log.
     */
    public function governanceScoreHistory(DataInitiative $dataInitiative): JsonResponse
    {
        $history = app(DataInitiativeGovernanceProjection::class)->history($dataInitiative);

        return response()->json(['data' => $history]);
    }
}
