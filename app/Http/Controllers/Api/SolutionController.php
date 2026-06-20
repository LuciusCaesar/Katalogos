<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSolutionRequest;
use App\Http\Requests\Api\UpdateSolutionRequest;
use App\Models\Solution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class SolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(Solution::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSolutionRequest $request): JsonResource
    {
        $solution = Solution::create($request->validated());

        return new JsonResource($solution);
    }

    /**
     * Display the specified resource.
     */
    public function show(Solution $solution): JsonResource
    {
        return new JsonResource($solution);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSolutionRequest $request, Solution $solution): JsonResource
    {
        $solution->update($request->validated());

        return new JsonResource($solution);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Solution $solution): JsonResponse
    {
        $solution->delete();

        return response()->json(null, 204);
    }
}
