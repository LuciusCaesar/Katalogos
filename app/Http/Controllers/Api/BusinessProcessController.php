<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBusinessProcessRequest;
use App\Http\Requests\Api\UpdateBusinessProcessRequest;
use App\Http\Resources\BusinessProcessResource;
use App\Models\BusinessProcess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BusinessProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return BusinessProcessResource::collection(BusinessProcess::with('businessAssets')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusinessProcessRequest $request): BusinessProcessResource
    {
        $businessProcess = BusinessProcess::create($request->validated());

        if ($request->has('business_asset_ids')) {
            $businessProcess->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return new BusinessProcessResource($businessProcess->load('businessAssets'));
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessProcess $businessProcess): BusinessProcessResource
    {
        return new BusinessProcessResource($businessProcess->load('businessAssets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusinessProcessRequest $request, BusinessProcess $businessProcess): BusinessProcessResource
    {
        $businessProcess->update($request->validated());

        if ($request->has('business_asset_ids')) {
            $businessProcess->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return new BusinessProcessResource($businessProcess->load('businessAssets'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessProcess $businessProcess): JsonResponse
    {
        $businessProcess->delete();

        return response()->json(null, 204);
    }
}
