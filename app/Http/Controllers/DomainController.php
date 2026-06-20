<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Models\Domain;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DomainController extends Controller
{
    /**
     * Display a listing of the domains.
     */
    public function index(): View
    {
        $domains = Domain::withCount('businessAssets')
            ->latest()
            ->paginate(10);

        return view('pages.domains.index', compact('domains'));
    }

    /**
     * Show the form for creating a new domain.
     */
    public function create(): View
    {
        return view('pages.domains.create');
    }

    /**
     * Store a newly created domain in storage.
     */
    public function store(StoreDomainRequest $request): RedirectResponse
    {
        Domain::create($request->validated());

        return redirect()
            ->route('web.domains.index')
            ->with('success', __('Domain created successfully.'));
    }

    /**
     * Display the specified domain.
     */
    public function show(Domain $domain): View
    {
        $domain->load(['businessAssets.dataInitiative']);

        return view('pages.domains.show', compact('domain'));
    }

    /**
     * Show the form for editing the specified domain.
     */
    public function edit(Domain $domain): View
    {
        return view('pages.domains.edit', compact('domain'));
    }

    /**
     * Update the specified domain in storage.
     */
    public function update(UpdateDomainRequest $request, Domain $domain): RedirectResponse
    {
        $domain->update($request->validated());

        return redirect()
            ->route('web.domains.index')
            ->with('success', __('Domain updated successfully.'));
    }

    /**
     * Remove the specified domain from storage.
     */
    public function destroy(Domain $domain): RedirectResponse
    {
        $domain->delete();

        return redirect()
            ->route('web.domains.index')
            ->with('success', __('Domain deleted successfully.'));
    }
}
