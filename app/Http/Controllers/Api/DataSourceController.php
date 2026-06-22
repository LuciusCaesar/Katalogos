<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDataSourceRequest;
use App\Http\Requests\Api\UpdateDataSourceRequest;
use App\Models\DataSource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DataSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(DataSource::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDataSourceRequest $request): JsonResource
    {
        $dataSource = DataSource::create($request->validated());

        return new JsonResource($dataSource);
    }

    /**
     * Display the specified resource.
     */
    public function show(DataSource $dataSource): JsonResource
    {
        return new JsonResource($dataSource);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDataSourceRequest $request, DataSource $dataSource): JsonResource
    {
        $dataSource->update($request->validated());

        return new JsonResource($dataSource);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataSource $dataSource): JsonResponse
    {
        $dataSource->delete();

        return response()->json(null, 204);
    }
}
