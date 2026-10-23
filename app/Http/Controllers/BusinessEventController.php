<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessEventRequest;
use App\Http\Requests\UpdateBusinessEventRequest;
use App\Models\BusinessAsset;
use App\Models\BusinessEvent;
use App\Models\BusinessProcess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessEventController extends Controller
{
    /**
     * Display a listing of the business events.
     */
    public function index(Request $request): View
    {
        $businessEvents = BusinessEvent::with(['businessAssets', 'businessProcesses'])
            ->latest()
            ->paginate(10);

        return view('pages.business-events.index', compact('businessEvents'));
    }

    /**
     * Show the form for creating a new business event.
     */
    public function create(): View
    {
        $businessAssets = BusinessAsset::latest()->get();
        $businessProcesses = BusinessProcess::latest()->get();

        return view('pages.business-events.create', compact('businessAssets', 'businessProcesses'));
    }

    /**
     * Store a newly created business event in storage.
     */
    public function store(StoreBusinessEventRequest $request): RedirectResponse
    {
        $businessEvent = BusinessEvent::create($request->validated());

        $this->syncRelations($businessEvent, $request);

        return redirect()
            ->route('web.business-events.index')
            ->with('success', __('Business Event created successfully.'));
    }

    /**
     * Display the specified business event.
     */
    public function show(BusinessEvent $businessEvent): View
    {
        $businessEvent->load(['businessAssets', 'businessProcesses']);

        return view('pages.business-events.show', compact('businessEvent'));
    }

    /**
     * Show the form for editing the specified business event.
     */
    public function edit(BusinessEvent $businessEvent): View
    {
        $businessAssets = BusinessAsset::latest()->get();
        $businessProcesses = BusinessProcess::latest()->get();

        return view('pages.business-events.edit', compact('businessEvent', 'businessAssets', 'businessProcesses'));
    }

    /**
     * Update the specified business event in storage.
     */
    public function update(UpdateBusinessEventRequest $request, BusinessEvent $businessEvent): RedirectResponse
    {
        $businessEvent->update($request->validated());

        $this->syncRelations($businessEvent, $request);

        return redirect()
            ->route('web.business-events.index')
            ->with('success', __('Business Event updated successfully.'));
    }

    /**
     * Remove the specified business event from storage.
     */
    public function destroy(BusinessEvent $businessEvent): RedirectResponse
    {
        $businessEvent->delete();

        return redirect()
            ->route('web.business-events.index')
            ->with('success', __('Business Event deleted successfully.'));
    }

    /**
     * Sync the business asset and business process relations of a business
     * event when the corresponding ids are present in the request.
     */
    private function syncRelations(BusinessEvent $businessEvent, Request $request): void
    {
        if ($request->has('business_asset_ids')) {
            $businessEvent->businessAssets()->sync($request->input('business_asset_ids'));
        }

        if ($request->has('business_process_ids')) {
            $businessEvent->businessProcesses()->sync($request->input('business_process_ids'));
        }
    }
}
