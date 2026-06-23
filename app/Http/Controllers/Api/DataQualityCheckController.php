<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDataQualityCheckRequest;
use App\Http\Requests\Api\UpdateDataQualityCheckRequest;
use App\Models\DataQualityCheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DataQualityCheckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(DataQualityCheck::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDataQualityCheckRequest $request): JsonResource
    {
        $dataQualityCheck = DataQualityCheck::create($request->validated());

        return new JsonResource($dataQualityCheck);
    }

    /**
     * Display the specified resource.
     */
    public function show(DataQualityCheck $dataQualityCheck): JsonResource
    {
        return new JsonResource($dataQualityCheck);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDataQualityCheckRequest $request, DataQualityCheck $dataQualityCheck): JsonResource
    {
        $dataQualityCheck->update($request->validated());

        return new JsonResource($dataQualityCheck);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataQualityCheck $dataQualityCheck): JsonResponse
    {
        $dataQualityCheck->delete();

        return response()->json(null, 204);
    }
}
