<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDataIssueRequest;
use App\Http\Requests\UpdateDataIssueRequest;
use App\Models\BusinessAsset;
use App\Models\DataIssue;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DataIssueController extends Controller
{
    /**
     * Display a listing of the data issues.
     */
    public function index(): View
    {
        $dataIssues = DataIssue::withCount('businessAssets')
            ->latest()
            ->paginate(10);

        return view('pages.data-issues.index', compact('dataIssues'));
    }

    /**
     * Show the form for creating a new data issue.
     */
    public function create(): View
    {
        $businessAssets = BusinessAsset::latest()->get();

        return view('pages.data-issues.create', compact('businessAssets'));
    }

    /**
     * Store a newly created data issue in storage.
     */
    public function store(StoreDataIssueRequest $request): RedirectResponse
    {
        $dataIssue = DataIssue::create($request->validated());

        if ($request->has('business_asset_ids')) {
            $dataIssue->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return redirect()
            ->route('web.data-issues.index')
            ->with('success', __('Data issue created successfully.'));
    }

    /**
     * Display the specified data issue.
     */
    public function show(DataIssue $dataIssue): View
    {
        $dataIssue->load(['businessAssets.dataInitiative', 'businessAssets.domain']);

        return view('pages.data-issues.show', compact('dataIssue'));
    }

    /**
     * Show the form for editing the specified data issue.
     */
    public function edit(DataIssue $dataIssue): View
    {
        $businessAssets = BusinessAsset::latest()->get();

        return view('pages.data-issues.edit', compact('dataIssue', 'businessAssets'));
    }

    /**
     * Update the specified data issue in storage.
     */
    public function update(UpdateDataIssueRequest $request, DataIssue $dataIssue): RedirectResponse
    {
        $dataIssue->update($request->validated());

        if ($request->has('business_asset_ids')) {
            $dataIssue->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return redirect()
            ->route('web.data-issues.index')
            ->with('success', __('Data issue updated successfully.'));
    }

    /**
     * Remove the specified data issue from storage.
     */
    public function destroy(DataIssue $dataIssue): RedirectResponse
    {
        $dataIssue->delete();

        return redirect()
            ->route('web.data-issues.index')
            ->with('success', __('Data issue deleted successfully.'));
    }
}
