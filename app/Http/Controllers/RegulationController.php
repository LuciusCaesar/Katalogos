<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegulationRequest;
use App\Http\Requests\UpdateRegulationRequest;
use App\Models\BusinessAsset;
use App\Models\Regulation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegulationController extends Controller
{
    /**
     * Display a listing of the regulations.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search', '');

        $query = Regulation::with(['businessAssets']);

        if ($search !== '') {
            $query = $query->search($search);
        }

        $regulations = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.regulations.index', compact('regulations', 'search'));
    }

    /**
     * Show the form for creating a new regulation.
     */
    public function create(): View
    {
        $businessAssets = BusinessAsset::latest()->get();

        return view('pages.regulations.create', compact('businessAssets'));
    }

    /**
     * Store a newly created regulation in storage.
     */
    public function store(StoreRegulationRequest $request): RedirectResponse
    {
        $regulation = Regulation::create($request->validated());

        if ($request->has('business_asset_ids')) {
            $regulation->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return redirect()
            ->route('web.regulations.index')
            ->with('success', __('Regulation created successfully.'));
    }

    /**
     * Display the specified regulation.
     */
    public function show(Regulation $regulation): View
    {
        $regulation->load(['businessAssets']);

        return view('pages.regulations.show', compact('regulation'));
    }

    /**
     * Show the form for editing the specified regulation.
     */
    public function edit(Regulation $regulation): View
    {
        $businessAssets = BusinessAsset::latest()->get();

        return view('pages.regulations.edit', compact('regulation', 'businessAssets'));
    }

    /**
     * Update the specified regulation in storage.
     */
    public function update(UpdateRegulationRequest $request, Regulation $regulation): RedirectResponse
    {
        $regulation->update($request->validated());

        if ($request->has('business_asset_ids')) {
            $regulation->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return redirect()
            ->route('web.regulations.index')
            ->with('success', __('Regulation updated successfully.'));
    }

    /**
     * Remove the specified regulation from storage.
     */
    public function destroy(Regulation $regulation): RedirectResponse
    {
        $regulation->delete();

        return redirect()
            ->route('web.regulations.index')
            ->with('success', __('Regulation deleted successfully.'));
    }
}
