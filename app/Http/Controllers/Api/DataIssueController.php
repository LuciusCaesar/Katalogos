<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDataIssueRequest;
use App\Http\Requests\Api\UpdateDataIssueRequest;
use App\Models\DataIssue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DataIssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(DataIssue::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDataIssueRequest $request): JsonResource
    {
        $dataIssue = DataIssue::create($request->validated());

        return new JsonResource($dataIssue);
    }

    /**
     * Display the specified resource.
     */
    public function show(DataIssue $dataIssue): JsonResource
    {
        return new JsonResource($dataIssue);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDataIssueRequest $request, DataIssue $dataIssue): JsonResource
    {
        $dataIssue->update($request->validated());

        return new JsonResource($dataIssue);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataIssue $dataIssue): JsonResponse
    {
        $dataIssue->delete();

        return response()->json(null, 204);
    }
}
