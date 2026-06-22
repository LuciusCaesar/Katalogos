<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDataSourceRequest;
use App\Http\Requests\UpdateDataSourceRequest;
use App\Models\BusinessAsset;
use App\Models\DataSource;
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
        $dataSource->load(['businessAssets.dataInitiative', 'businessAssets.domain']);

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
}
