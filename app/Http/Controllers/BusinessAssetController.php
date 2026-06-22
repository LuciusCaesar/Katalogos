<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessAssetRequest;
use App\Http\Requests\UpdateBusinessAssetRequest;
use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\Domain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessAssetController extends Controller
{
    /**
     * Display a listing of the business assets.
     */
    public function index(Request $request): View
    {
        $businessAssets = BusinessAsset::with(['dataInitiative', 'domain', 'dataSteward', 'dataOwner'])
            ->latest()
            ->paginate(10);

        return view('pages.business-assets.index', compact('businessAssets'));
    }

    /**
     * Show the form for creating a new business asset.
     */
    public function create(): View
    {
        $domains = Domain::all();
        $dataInitiatives = DataInitiative::all();

        return view('pages.business-assets.create', compact('domains', 'dataInitiatives'));
    }

    /**
     * Store a newly created business asset in storage.
     */
    public function store(StoreBusinessAssetRequest $request): RedirectResponse
    {
        BusinessAsset::create($request->validated());

        return redirect()
            ->route('web.business-assets.index')
            ->with('success', __('Business Asset created successfully.'));
    }

    /**
     * Display the specified business asset.
     */
    public function show(BusinessAsset $businessAsset): View
    {
        $businessAsset->load(['dataInitiative', 'domain', 'dataSteward', 'dataOwner', 'businessRules', 'dataIssues']);

        return view('pages.business-assets.show', compact('businessAsset'));
    }

    /**
     * Show the form for editing the specified business asset.
     */
    public function edit(BusinessAsset $businessAsset): View
    {
        $domains = Domain::all();
        $dataInitiatives = DataInitiative::all();

        return view('pages.business-assets.edit', compact('businessAsset', 'domains', 'dataInitiatives'));
    }

    /**
     * Update the specified business asset in storage.
     */
    public function update(UpdateBusinessAssetRequest $request, BusinessAsset $businessAsset): RedirectResponse
    {
        $businessAsset->update($request->validated());

        return redirect()
            ->route('web.business-assets.index')
            ->with('success', __('Business Asset updated successfully.'));
    }

    /**
     * Remove the specified business asset from storage.
     */
    public function destroy(BusinessAsset $businessAsset): RedirectResponse
    {
        $businessAsset->delete();

        return redirect()
            ->route('web.business-assets.index')
            ->with('success', __('Business Asset deleted successfully.'));
    }
}
