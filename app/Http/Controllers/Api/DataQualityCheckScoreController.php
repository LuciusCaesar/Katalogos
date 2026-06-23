<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDataQualityCheckScoreRequest;
use App\Http\Resources\DataQualityCheckScoreResource;
use App\Models\DataQualityCheck;
use App\Models\DataQualityCheckScore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DataQualityCheckScoreController extends Controller
{
    /**
     * Display a listing of scores for a data quality check.
     */
    public function index(DataQualityCheck $dataQualityCheck): AnonymousResourceCollection
    {
        return DataQualityCheckScoreResource::collection(
            $dataQualityCheck->scores()->with('origin')->latest()->get()
        );
    }

    /**
     * Store a newly created score for a data quality check.
     */
    public function store(StoreDataQualityCheckScoreRequest $request, DataQualityCheck $dataQualityCheck): JsonResponse
    {
        $validated = $request->validated();
        $validated['data_quality_check_id'] = $dataQualityCheck->id;

        $score = DataQualityCheckScore::create($validated);

        return (new DataQualityCheckScoreResource($score))
            ->response()
            ->setStatusCode(201);
    }
}
