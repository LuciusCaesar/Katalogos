<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBusinessRuleRequest;
use App\Http\Requests\Api\UpdateBusinessRuleRequest;
use App\Models\BusinessRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(BusinessRule::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusinessRuleRequest $request): JsonResource
    {
        $businessRule = BusinessRule::create($request->validated());

        return new JsonResource($businessRule);
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessRule $businessRule): JsonResource
    {
        return new JsonResource($businessRule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusinessRuleRequest $request, BusinessRule $businessRule): JsonResource
    {
        $businessRule->update($request->validated());

        return new JsonResource($businessRule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessRule $businessRule): JsonResponse
    {
        $businessRule->delete();

        return response()->json(null, 204);
    }
}
