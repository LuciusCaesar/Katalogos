<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessRuleRequest;
use App\Http\Requests\UpdateBusinessRuleRequest;
use App\Models\BusinessAsset;
use App\Models\BusinessRule;
use App\Models\DataIssue;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusinessRuleController extends Controller
{
    /**
     * Display a listing of the business rules.
     */
    public function index(): View
    {
        $businessRules = BusinessRule::withCount(['businessAssets', 'dataIssues', 'dataQualityChecks'])
            ->with(['dataQualityChecks' => function ($query) {
                $query->with('latestScore');
            }])
            ->latest()
            ->paginate(10);

        return view('pages.business-rules.index', compact('businessRules'));
    }

    /**
     * Show the form for creating a new business rule.
     */
    public function create(): View
    {
        $businessAssets = BusinessAsset::latest()->get();
        $dataIssues = DataIssue::latest()->get();

        return view('pages.business-rules.create', compact('businessAssets', 'dataIssues'));
    }

    /**
     * Store a newly created business rule in storage.
     */
    public function store(StoreBusinessRuleRequest $request): RedirectResponse
    {
        $businessRule = BusinessRule::create($request->validated());

        if ($request->has('business_asset_ids')) {
            $businessRule->businessAssets()->sync($request->input('business_asset_ids'));
        }

        if ($request->has('data_issue_ids')) {
            $businessRule->dataIssues()->sync($request->input('data_issue_ids'));
        }

        return redirect()
            ->route('web.business-rules.index')
            ->with('success', __('Business rule created successfully.'));
    }

    /**
     * Display the specified business rule.
     */
    public function show(BusinessRule $businessRule): View
    {
        $businessRule->load([
            'businessAssets.dataInitiative',
            'businessAssets.domain',
            'dataIssues',
            'dataQualityChecks' => function ($query) {
                $query->with(['dataSources', 'latestScore']);
            },
        ]);

        return view('pages.business-rules.show', compact('businessRule'));
    }

    /**
     * Show the form for editing the specified business rule.
     */
    public function edit(BusinessRule $businessRule): View
    {
        $businessAssets = BusinessAsset::latest()->get();
        $dataIssues = DataIssue::latest()->get();

        return view('pages.business-rules.edit', compact('businessRule', 'businessAssets', 'dataIssues'));
    }

    /**
     * Update the specified business rule in storage.
     */
    public function update(UpdateBusinessRuleRequest $request, BusinessRule $businessRule): RedirectResponse
    {
        $businessRule->update($request->validated());

        if ($request->has('business_asset_ids')) {
            $businessRule->businessAssets()->sync($request->input('business_asset_ids'));
        }

        if ($request->has('data_issue_ids')) {
            $businessRule->dataIssues()->sync($request->input('data_issue_ids'));
        }

        return redirect()
            ->route('web.business-rules.index')
            ->with('success', __('Business rule updated successfully.'));
    }

    /**
     * Remove the specified business rule from storage.
     */
    public function destroy(BusinessRule $businessRule): RedirectResponse
    {
        $businessRule->delete();

        return redirect()
            ->route('web.business-rules.index')
            ->with('success', __('Business rule deleted successfully.'));
    }
}
