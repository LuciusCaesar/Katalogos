<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDataInitiativeRequest;
use App\Http\Requests\Api\UpdateDataInitiativeRequest;
use App\Models\DataInitiative;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DataInitiativeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(DataInitiative::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDataInitiativeRequest $request): JsonResource
    {
        $dataInitiative = DataInitiative::create($request->validated());

        return new JsonResource($dataInitiative);
    }

    /**
     * Display the specified resource.
     */
    public function show(DataInitiative $dataInitiative): JsonResource
    {
        return new JsonResource($dataInitiative);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDataInitiativeRequest $request, DataInitiative $dataInitiative): JsonResource
    {
        $dataInitiative->update($request->validated());

        return new JsonResource($dataInitiative);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataInitiative $dataInitiative): JsonResponse
    {
        $dataInitiative->delete();

        return response()->json(null, 204);
    }
}
