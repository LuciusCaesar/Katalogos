<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDataSourceRequest;
use App\Http\Requests\Api\UpdateDataSourceRequest;
use App\Http\Requests\UpdateDataSourceTeamRequest;
use App\Http\Resources\DataSourceResource;
use App\Models\DataSource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DataSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return DataSourceResource::collection(DataSource::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDataSourceRequest $request): DataSourceResource
    {
        $dataSource = DataSource::create($request->validated());

        return new DataSourceResource($dataSource);
    }

    /**
     * Display the specified resource.
     */
    public function show(DataSource $dataSource): DataSourceResource
    {
        return new DataSourceResource($dataSource);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDataSourceRequest $request, DataSource $dataSource): DataSourceResource
    {
        $dataSource->update($request->validated());

        return new DataSourceResource($dataSource);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataSource $dataSource): JsonResponse
    {
        $dataSource->delete();

        return response()->json(null, 204);
    }

    /**
     * Update the team for a data source.
     */
    public function updateTeam(UpdateDataSourceTeamRequest $request, DataSource $dataSource): DataSourceResource
    {
        if ($request->filled('data_custodian_id')) {
            $dataCustodianId = $request->integer('data_custodian_id');
            $user = User::findOrFail($dataCustodianId);

            $existingCustodian = $dataSource->dataCustodian()->first();

            if (! $existingCustodian || $existingCustodian->id !== $user->id) {
                if ($existingCustodian) {
                    $dataSource->removeDataCustodian();
                }
                $dataSource->assignDataCustodian($user);
            }
        } else {
            $existingCustodian = $dataSource->dataCustodian()->first();
            if ($existingCustodian) {
                $dataSource->removeDataCustodian();
            }
        }

        return new DataSourceResource($dataSource);
    }
}
