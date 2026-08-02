<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessProcessRequest;
use App\Http\Requests\UpdateBusinessProcessRequest;
use App\Models\BusinessAsset;
use App\Models\BusinessProcess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessProcessController extends Controller
{
    /**
     * Display a listing of the business processes.
     */
    public function index(Request $request): View
    {
        $businessProcesses = BusinessProcess::with(['businessAssets'])
            ->latest()
            ->paginate(10);

        return view('pages.business-processes.index', compact('businessProcesses'));
    }

    /**
     * Show the form for creating a new business process.
     */
    public function create(): View
    {
        $businessAssets = BusinessAsset::latest()->get();

        return view('pages.business-processes.create', compact('businessAssets'));
    }

    /**
     * Store a newly created business process in storage.
     */
    public function store(StoreBusinessProcessRequest $request): RedirectResponse
    {
        $businessProcess = BusinessProcess::create($request->validated());

        if ($request->has('business_asset_ids')) {
            $businessProcess->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return redirect()
            ->route('web.business-processes.index')
            ->with('success', __('Business Process created successfully.'));
    }

    /**
     * Display the specified business process.
     */
    public function show(BusinessProcess $businessProcess): View
    {
        $businessProcess->load(['businessAssets']);

        return view('pages.business-processes.show', compact('businessProcess'));
    }

    /**
     * Show the form for editing the specified business process.
     */
    public function edit(BusinessProcess $businessProcess): View
    {
        $businessAssets = BusinessAsset::latest()->get();

        return view('pages.business-processes.edit', compact('businessProcess', 'businessAssets'));
    }

    /**
     * Update the specified business process in storage.
     */
    public function update(UpdateBusinessProcessRequest $request, BusinessProcess $businessProcess): RedirectResponse
    {
        $businessProcess->update($request->validated());

        if ($request->has('business_asset_ids')) {
            $businessProcess->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return redirect()
            ->route('web.business-processes.index')
            ->with('success', __('Business Process updated successfully.'));
    }

    /**
     * Remove the specified business process from storage.
     */
    public function destroy(BusinessProcess $businessProcess): RedirectResponse
    {
        $businessProcess->delete();

        return redirect()
            ->route('web.business-processes.index')
            ->with('success', __('Business Process deleted successfully.'));
    }
}
