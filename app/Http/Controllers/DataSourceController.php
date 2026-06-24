<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDataSourceRequest;
use App\Http\Requests\UpdateDataSourceRequest;
use App\Http\Requests\UpdateDataSourceTeamRequest;
use App\Models\BusinessAsset;
use App\Models\DataSource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DataSourceController extends Controller
{
    /**
     * Display a listing of the data sources.
     */
    public function index(): View
    {
        $dataSources = DataSource::withCount('businessAssets')
            ->with('dataCustodian')
            ->latest()
            ->paginate(10);

        return view('pages.data-sources.index', compact('dataSources'));
    }

    /**
     * Show the form for creating a new data source.
     */
    public function create(): View
    {
        $businessAssets = BusinessAsset::latest()->get();

        return view('pages.data-sources.create', compact('businessAssets'));
    }

    /**
     * Store a newly created data source in storage.
     */
    public function store(StoreDataSourceRequest $request): RedirectResponse
    {
        $dataSource = DataSource::create($request->validated());

        if ($request->has('business_asset_ids')) {
            $dataSource->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return redirect()
            ->route('web.data-sources.index')
            ->with('success', __('Data source created successfully.'));
    }

    /**
     * Display the specified data source.
     */
    public function show(DataSource $dataSource): View
    {
        $dataSource->load(['businessAssets.dataInitiative', 'businessAssets.domain', 'dataCustodian']);

        return view('pages.data-sources.show', compact('dataSource'));
    }

    /**
     * Show the form for editing the specified data source.
     */
    public function edit(DataSource $dataSource): View
    {
        $businessAssets = BusinessAsset::latest()->get();

        return view('pages.data-sources.edit', compact('dataSource', 'businessAssets'));
    }

    /**
     * Update the specified data source in storage.
     */
    public function update(UpdateDataSourceRequest $request, DataSource $dataSource): RedirectResponse
    {
        $dataSource->update($request->validated());

        if ($request->has('business_asset_ids')) {
            $dataSource->businessAssets()->sync($request->input('business_asset_ids'));
        }

        return redirect()
            ->route('web.data-sources.index')
            ->with('success', __('Data source updated successfully.'));
    }

    /**
     * Remove the specified data source from storage.
     */
    public function destroy(DataSource $dataSource): RedirectResponse
    {
        $dataSource->delete();

        return redirect()
            ->route('web.data-sources.index')
            ->with('success', __('Data source deleted successfully.'));
    }

    /**
     * Show the form for editing the team for the specified data source.
     */
    public function editTeam(DataSource $dataSource): View
    {
        $users = User::all();

        return view('pages.data-sources.manage-team', compact('dataSource', 'users'));
    }

    /**
     * Update the team for the specified data source in storage.
     */
    public function updateTeam(UpdateDataSourceTeamRequest $request, DataSource $dataSource): RedirectResponse
    {
        $dataCustodianRole = Role::where('name', 'Data Custodian')->firstOrFail();

        // Handle Data Custodian assignment
        if ($request->filled('data_custodian_id')) {
            $dataCustodianId = $request->integer('data_custodian_id');
            $user = User::findOrFail($dataCustodianId);

            // Check if user already has this role
            $existingCustodian = $dataSource->dataCustodian()->first();
            $userAlreadyAssigned = $existingCustodian && $existingCustodian->id === $user->id;

            if (! $userAlreadyAssigned) {
                // Remove existing Data Custodian if different user
                if ($existingCustodian) {
                    $dataSource->removeRoleFromUser($existingCustodian, $dataCustodianRole);
                }

                // Assign new Data Custodian
                $dataSource->assignRoleToUser($user, $dataCustodianRole);
            }
        } else {
            // Remove Data Custodian if null selected
            $existingCustodian = $dataSource->dataCustodian()->first();
            if ($existingCustodian) {
                $dataSource->removeRoleFromUser($existingCustodian, $dataCustodianRole);
            }
        }

        return redirect()
            ->route('web.data-sources.show', $dataSource)
            ->with('success', __('Team updated successfully.'));
    }
}
