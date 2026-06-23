<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBusinessAssetRequest;
use App\Http\Requests\Api\UpdateBusinessAssetRequest;
use App\Http\Resources\BusinessAssetResource;
use App\Models\BusinessAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BusinessAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return BusinessAssetResource::collection(
            BusinessAsset::with(['governanceScore'])->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusinessAssetRequest $request): BusinessAssetResource
    {
        $businessAsset = BusinessAsset::create($request->validated());
        $businessAsset->load('governanceScore');

        return new BusinessAssetResource($businessAsset);
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessAsset $businessAsset): BusinessAssetResource
    {
        $businessAsset->load(['governanceScore']);

        return new BusinessAssetResource($businessAsset);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusinessAssetRequest $request, BusinessAsset $businessAsset): BusinessAssetResource
    {
        $businessAsset->update($request->validated());
        $businessAsset->load('governanceScore');

        return new BusinessAssetResource($businessAsset);
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
