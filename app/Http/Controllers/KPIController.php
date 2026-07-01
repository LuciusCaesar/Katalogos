<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKPIRequest;
use App\Http\Requests\UpdateKPIRequest;
use App\Models\BusinessObjective;
use App\Models\KPI;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KPIController extends Controller
{
    /**
     * Display a listing of the KPIs.
     */
    public function index(Request $request): View
    {
        $kpis = KPI::with(['businessObjective'])
            ->latest()
            ->paginate(10);

        return view('pages.kpis.index', compact('kpis'));
    }

    /**
     * Show the form for creating a new KPI.
     */
    public function create(): View
    {
        $businessObjectives = BusinessObjective::orderBy('name')->get();

        return view('pages.kpis.create', compact('businessObjectives'));
    }

    /**
     * Store a newly created KPI in storage.
     */
    public function store(StoreKPIRequest $request): RedirectResponse
    {
        KPI::create($request->validated());

        return redirect()
            ->route('web.kpis.index')
            ->with('success', __('KPI created successfully.'));
    }

    /**
     * Display the specified KPI.
     */
    public function show(KPI $kpi): View
    {
        $kpi->load(['businessObjective']);

        return view('pages.kpis.show', compact('kpi'));
    }

    /**
     * Show the form for editing the specified KPI.
     */
    public function edit(KPI $kpi): View
    {
        $businessObjectives = BusinessObjective::orderBy('name')->get();

        return view('pages.kpis.edit', compact('kpi', 'businessObjectives'));
    }

    /**
     * Update the specified KPI in storage.
     */
    public function update(UpdateKPIRequest $request, KPI $kpi): RedirectResponse
    {
        $kpi->update($request->validated());

        return redirect()
            ->route('web.kpis.index')
            ->with('success', __('KPI updated successfully.'));
    }

    /**
     * Remove the specified KPI from storage.
     */
    public function destroy(KPI $kpi): RedirectResponse
    {
        $kpi->delete();

        return redirect()
            ->route('web.kpis.index')
            ->with('success', __('KPI deleted successfully.'));
    }
}
