<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreKPIRequest;
use App\Http\Requests\Api\UpdateKPIRequest;
use App\Http\Resources\KPIResource;
use App\Models\KPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KPIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return KPIResource::collection(KPI::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKPIRequest $request): KPIResource
    {
        $kpi = KPI::create($request->validated());

        return new KPIResource($kpi);
    }

    /**
     * Display the specified resource.
     */
    public function show(KPI $kpi): KPIResource
    {
        return new KPIResource($kpi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKPIRequest $request, KPI $kpi): KPIResource
    {
        $kpi->update($request->validated());

        return new KPIResource($kpi);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KPI $kpi): JsonResponse
    {
        $kpi->delete();

        return response()->json(null, 204);
    }
}
