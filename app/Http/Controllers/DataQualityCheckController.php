<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDataQualityCheckRequest;
use App\Http\Requests\UpdateDataQualityCheckRequest;
use App\Models\BusinessRule;
use App\Models\DataQualityCheck;
use App\Models\DataSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DataQualityCheckController extends Controller
{
    /**
     * Display a listing of the data quality checks.
     */
    public function index(): View
    {
        $dataQualityChecks = DataQualityCheck::withCount(['dataSources'])
            ->with('businessRule')
            ->latest()
            ->paginate(10);

        return view('pages.data-quality-checks.index', compact('dataQualityChecks'));
    }

    /**
     * Show the form for creating a new data quality check.
     */
    public function create(): View
    {
        $businessRules = BusinessRule::latest()->get();
        $dataSources = DataSource::latest()->get();

        return view('pages.data-quality-checks.create', compact('businessRules', 'dataSources'));
    }

    /**
     * Store a newly created data quality check in storage.
     */
    public function store(StoreDataQualityCheckRequest $request): RedirectResponse
    {
        $dataQualityCheck = DataQualityCheck::create($request->validated());

        if ($request->has('data_source_ids')) {
            $dataQualityCheck->dataSources()->sync($request->input('data_source_ids'));
        }

        return redirect()
            ->route('web.data-quality-checks.index')
            ->with('success', __('Data quality check created successfully.'));
    }

    /**
     * Display the specified data quality check.
     */
    public function show(DataQualityCheck $dataQualityCheck): View
    {
        $dataQualityCheck->load([
            'businessRule',
            'dataSources',
            'latestScore',
            'latestScore.origin',
        ]);

        return view('pages.data-quality-checks.show', compact('dataQualityCheck'));
    }

    /**
     * Show the form for editing the specified data quality check.
     */
    public function edit(DataQualityCheck $dataQualityCheck): View
    {
        $businessRules = BusinessRule::latest()->get();
        $dataSources = DataSource::latest()->get();

        return view('pages.data-quality-checks.edit', compact('dataQualityCheck', 'businessRules', 'dataSources'));
    }

    /**
     * Update the specified data quality check in storage.
     */
    public function update(UpdateDataQualityCheckRequest $request, DataQualityCheck $dataQualityCheck): RedirectResponse
    {
        $dataQualityCheck->update($request->validated());

        if ($request->has('data_source_ids')) {
            $dataQualityCheck->dataSources()->sync($request->input('data_source_ids'));
        }

        return redirect()
            ->route('web.data-quality-checks.index')
            ->with('success', __('Data quality check updated successfully.'));
    }

    /**
     * Remove the specified data quality check from storage.
     */
    public function destroy(DataQualityCheck $dataQualityCheck): RedirectResponse
    {
        $dataQualityCheck->delete();

        return redirect()
            ->route('web.data-quality-checks.index')
            ->with('success', __('Data quality check deleted successfully.'));
    }
}
