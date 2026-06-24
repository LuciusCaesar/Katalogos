<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDomainRequest;
use App\Http\Requests\Api\UpdateDomainRequest;
use App\Http\Requests\UpdateDomainTeamRequest;
use App\Http\Resources\DomainResource;
use App\Models\Domain;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return DomainResource::collection(Domain::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDomainRequest $request): DomainResource
    {
        $domain = Domain::create($request->validated());

        return new DomainResource($domain);
    }

    /**
     * Display the specified resource.
     */
    public function show(Domain $domain): DomainResource
    {
        return new DomainResource($domain);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDomainRequest $request, Domain $domain): DomainResource
    {
        $domain->update($request->validated());

        return new DomainResource($domain);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Domain $domain): JsonResponse
    {
        $domain->delete();

        return response()->json(null, 204);
    }

    /**
     * Update the team for a domain.
     */
    public function updateTeam(UpdateDomainTeamRequest $request, Domain $domain): DomainResource
    {
        $domainOwnerRole = Role::where('name', 'Domain Owner')->firstOrFail();

        if ($request->filled('domain_owner_id')) {
            $domainOwnerId = $request->integer('domain_owner_id');
            $user = User::findOrFail($domainOwnerId);

            $existingOwner = $domain->domainOwner()->first();

            if (! $existingOwner || $existingOwner->id !== $user->id) {
                if ($existingOwner) {
                    $domain->removeRoleFromUser($existingOwner, $domainOwnerRole);
                }
                $domain->assignRoleToUser($user, $domainOwnerRole);
            }
        } else {
            $existingOwner = $domain->domainOwner()->first();
            if ($existingOwner) {
                $domain->removeRoleFromUser($existingOwner, $domainOwnerRole);
            }
        }

        return new DomainResource($domain);
    }
}
