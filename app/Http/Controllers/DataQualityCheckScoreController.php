<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDataQualityCheckScoreRequest;
use App\Models\DataQualityCheck;
use App\Models\DataQualityCheckScore;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DataQualityCheckScoreController extends Controller
{
    /**
     * Display the score history for a data quality check.
     */
    public function index(DataQualityCheck $dataQualityCheck): View
    {
        $scores = $dataQualityCheck->scores()
            ->with('origin')
            ->latest()
            ->paginate(20);

        return view('pages.data-quality-checks.scores.index', compact('dataQualityCheck', 'scores'));
    }

    /**
     * Store a new score for a data quality check.
     */
    public function store(StoreDataQualityCheckScoreRequest $request, DataQualityCheck $dataQualityCheck): RedirectResponse
    {
        $validated = $request->validated();
        $validated['data_quality_check_id'] = $dataQualityCheck->id;

        DataQualityCheckScore::create($validated);

        return redirect()
            ->route('web.data-quality-checks.show', $dataQualityCheck)
            ->with('success', __('Score recorded successfully.'));
    }
}
