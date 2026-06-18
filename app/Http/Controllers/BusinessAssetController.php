<?php

namespace App\Http\Controllers;

use App\Models\BusinessAsset;
use Illuminate\Http\Request;

class BusinessAssetController extends Controller
{
    /**
     * Display a listing of the business assets.
     */
    public function index(Request $request)
    {
        $businessAssets = BusinessAsset::with(['dataInitiative', 'dataSteward', 'dataOwner'])
            ->latest()
            ->paginate(10);

        return view('pages.business-assets.index', compact('businessAssets'));
    }
}
