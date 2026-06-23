<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessAssetRequest;
use App\Http\Requests\UpdateBusinessAssetRequest;
use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\Domain;
use App\Models\GovernanceScore;
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
        $businessAssets = BusinessAsset::with([
            'dataInitiative',
            'domain',
            'dataSteward',
            'dataOwner',
            'governanceScore',
            'businessRules' => function ($query) {
                $query->with(['dataQualityChecks' => function ($query) {
                    $query->with('latestScore');
                }]);
            },
        ])
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
        $businessAsset->load([
            'dataInitiative',
            'domain',
            'dataSteward',
            'dataOwner',
            'businessRules' => function ($query) {
                $query->with(['dataQualityChecks' => function ($query) {
                    $query->with('latestScore');
                }]);
            },
            'dataIssues',
            'governanceScore',
            'governanceScores',
        ]);

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

    /**
     * Show governance score details for a business asset.
     */
    public function showGovernanceScoreDetails(BusinessAsset $businessAsset): View
    {
        $businessAsset->load(['governanceScore', 'governanceScores']);

        return view('pages.business-assets.governance-score-details', compact('businessAsset'));
    }

    /**
     * Show governance score history for a business asset.
     */
    public function showGovernanceScoreHistory(BusinessAsset $businessAsset): View
    {
        $businessAsset->load(['governanceScores' => fn ($query) => $query->orderBy('calculated_at', 'desc')]);

        return view('pages.business-assets.governance-score-history', compact('businessAsset'));
    }

    /**
     * Show details for a specific governance score.
     */
    public function showSpecificGovernanceScore(BusinessAsset $businessAsset, GovernanceScore $governanceScore): View
    {
        abort_unless($governanceScore->business_asset_id === $businessAsset->id, 404);

        return view('pages.business-assets.governance-score-show', compact('businessAsset', 'governanceScore'));
    }
}
