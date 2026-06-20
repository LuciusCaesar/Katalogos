<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBusinessAssetRequest;
use App\Http\Requests\Api\UpdateBusinessAssetRequest;
use App\Models\BusinessAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(BusinessAsset::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusinessAssetRequest $request): JsonResource
    {
        $businessAsset = BusinessAsset::create($request->validated());

        return new JsonResource($businessAsset);
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessAsset $businessAsset): JsonResource
    {
        return new JsonResource($businessAsset);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusinessAssetRequest $request, BusinessAsset $businessAsset): JsonResource
    {
        $businessAsset->update($request->validated());

        return new JsonResource($businessAsset);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessAsset $businessAsset): JsonResponse
    {
        $businessAsset->delete();

        return response()->json(null, 204);
    }
}
