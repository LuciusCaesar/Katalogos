<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRegulationRequest;
use App\Http\Requests\Api\UpdateRegulationRequest;
use App\Http\Resources\RegulationResource;
use App\Models\Regulation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RegulationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return RegulationResource::collection(Regulation::with('businessAssets')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegulationRequest $request): RegulationResource
    {
        $regulation = Regulation::create($request->validated());

        if ($request->has('business_asset_ids')) {
            $regulation->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return new RegulationResource($regulation->load('businessAssets'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Regulation $regulation): RegulationResource
    {
        return new RegulationResource($regulation->load('businessAssets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRegulationRequest $request, Regulation $regulation): RegulationResource
    {
        $regulation->update($request->validated());

        if ($request->has('business_asset_ids')) {
            $regulation->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return new RegulationResource($regulation->load('businessAssets'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Regulation $regulation): JsonResponse
    {
        $regulation->delete();

        return response()->json(null, 204);
    }
}
