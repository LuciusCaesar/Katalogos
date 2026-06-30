<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessObjectiveRequest;
use App\Http\Requests\UpdateBusinessObjectiveRequest;
use App\Models\BusinessObjective;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessObjectiveController extends Controller
{
    /**
     * Display a listing of the business objectives.
     */
    public function index(Request $request): View
    {
        $businessObjectives = BusinessObjective::with(['dataInitiatives'])
            ->latest()
            ->paginate(10);

        return view('pages.business-objectives.index', compact('businessObjectives'));
    }

    /**
     * Show the form for creating a new business objective.
     */
    public function create(): View
    {
        return view('pages.business-objectives.create');
    }

    /**
     * Store a newly created business objective in storage.
     */
    public function store(StoreBusinessObjectiveRequest $request): RedirectResponse
    {
        BusinessObjective::create($request->validated());

        return redirect()
            ->route('web.business-objectives.index')
            ->with('success', __('Business Objective created successfully.'));
    }

    /**
     * Display the specified business objective.
     */
    public function show(BusinessObjective $businessObjective): View
    {
        $businessObjective->load(['dataInitiatives']);

        return view('pages.business-objectives.show', compact('businessObjective'));
    }

    /**
     * Show the form for editing the specified business objective.
     */
    public function edit(BusinessObjective $businessObjective): View
    {
        return view('pages.business-objectives.edit', compact('businessObjective'));
    }

    /**
     * Update the specified business objective in storage.
     */
    public function update(UpdateBusinessObjectiveRequest $request, BusinessObjective $businessObjective): RedirectResponse
    {
        $businessObjective->update($request->validated());

        return redirect()
            ->route('web.business-objectives.index')
            ->with('success', __('Business Objective updated successfully.'));
    }

    /**
     * Remove the specified business objective from storage.
     */
    public function destroy(BusinessObjective $businessObjective): RedirectResponse
    {
        $businessObjective->delete();

        return redirect()
            ->route('web.business-objectives.index')
            ->with('success', __('Business Objective deleted successfully.'));
    }
}
