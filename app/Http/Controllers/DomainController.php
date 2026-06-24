<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Http\Requests\UpdateDomainTeamRequest;
use App\Models\Domain;
use App\Models\Role;
use App\Models\User;
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
            ->with('domainOwner')
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
        $domain->load(['businessAssets.dataInitiative', 'domainOwner']);

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

    /**
     * Show the form for editing the team for the specified domain.
     */
    public function editTeam(Domain $domain): View
    {
        $users = User::all();

        return view('pages.domains.manage-team', compact('domain', 'users'));
    }

    /**
     * Update the team for the specified domain in storage.
     */
    public function updateTeam(UpdateDomainTeamRequest $request, Domain $domain): RedirectResponse
    {
        $domainOwnerRole = Role::where('name', 'Domain Owner')->firstOrFail();

        // Handle Domain Owner assignment
        if ($request->filled('domain_owner_id')) {
            $domainOwnerId = $request->integer('domain_owner_id');
            $user = User::findOrFail($domainOwnerId);

            // Check if user already has this role
            $existingOwner = $domain->domainOwner()->first();
            $userAlreadyAssigned = $existingOwner && $existingOwner->id === $user->id;

            if (! $userAlreadyAssigned) {
                // Remove existing Domain Owner if different user
                if ($existingOwner) {
                    $domain->removeRoleFromUser($existingOwner, $domainOwnerRole);
                }

                // Assign new Domain Owner
                $domain->assignRoleToUser($user, $domainOwnerRole);
            }
        } else {
            // Remove Domain Owner if null selected
            $existingOwner = $domain->domainOwner()->first();
            if ($existingOwner) {
                $domain->removeRoleFromUser($existingOwner, $domainOwnerRole);
            }
        }

        return redirect()
            ->route('web.domains.show', $domain)
            ->with('success', __('Team updated successfully.'));
    }
}
