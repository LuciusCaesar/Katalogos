<?php

namespace App\Http\Controllers;

use App\Models\BusinessAsset;
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
     * Display the specified business asset.
     */
    public function show(BusinessAsset $businessAsset): View
    {
        $businessAsset->load(['dataInitiative', 'domain', 'dataSteward', 'dataOwner']);

        return view('pages.business-assets.show', compact('businessAsset'));
    }
}
