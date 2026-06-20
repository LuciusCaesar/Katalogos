<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRootCauseRequest;
use App\Http\Requests\Api\UpdateRootCauseRequest;
use App\Models\RootCause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class RootCauseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(RootCause::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRootCauseRequest $request): JsonResource
    {
        $rootCause = RootCause::create($request->validated());

        return new JsonResource($rootCause);
    }

    /**
     * Display the specified resource.
     */
    public function show(RootCause $rootCause): JsonResource
    {
        return new JsonResource($rootCause);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRootCauseRequest $request, RootCause $rootCause): JsonResource
    {
        $rootCause->update($request->validated());

        return new JsonResource($rootCause);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RootCause $rootCause): JsonResponse
    {
        $rootCause->delete();

        return response()->json(null, 204);
    }
}
