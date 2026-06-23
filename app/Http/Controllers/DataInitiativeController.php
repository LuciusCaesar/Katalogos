<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDataInitiativeRequest;
use App\Http\Requests\UpdateDataInitiativeRequest;
use App\Models\DataInitiative;
use App\Models\DataInitiativeGovernanceScoreHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataInitiativeController extends Controller
{
    /**
     * Display a listing of the data initiatives.
     */
    public function index(Request $request): View
    {
        $dataInitiatives = DataInitiative::with([
            'businessAssets',
            'dataSteward',
            'dataOwner',
            'governanceScoreHistory',
        ])
            ->latest()
            ->paginate(10);

        return view('pages.data-initiatives.index', compact('dataInitiatives'));
    }

    /**
     * Show the form for creating a new data initiative.
     */
    public function create(): View
    {
        return view('pages.data-initiatives.create');
    }

    /**
     * Store a newly created data initiative in storage.
     */
    public function store(StoreDataInitiativeRequest $request): RedirectResponse
    {
        DataInitiative::create($request->validated());

        return redirect()
            ->route('web.data-initiatives.index')
            ->with('success', __('Data Initiative created successfully.'));
    }

    /**
     * Display the specified data initiative.
     */
    public function show(DataInitiative $dataInitiative): View
    {
        $dataInitiative->load([
            'businessAssets',
            'dataSteward',
            'dataOwner',
            'governanceScoreHistory' => fn ($query) => $query->orderBy('calculated_at', 'desc'),
        ]);

        return view('pages.data-initiatives.show', compact('dataInitiative'));
    }

    /**
     * Show the form for editing the specified data initiative.
     */
    public function edit(DataInitiative $dataInitiative): View
    {
        return view('pages.data-initiatives.edit', compact('dataInitiative'));
    }

    /**
     * Update the specified data initiative in storage.
     */
    public function update(UpdateDataInitiativeRequest $request, DataInitiative $dataInitiative): RedirectResponse
    {
        $dataInitiative->update($request->validated());

        return redirect()
            ->route('web.data-initiatives.index')
            ->with('success', __('Data Initiative updated successfully.'));
    }

    /**
     * Remove the specified data initiative from storage.
     */
    public function destroy(DataInitiative $dataInitiative): RedirectResponse
    {
        $dataInitiative->delete();

        return redirect()
            ->route('web.data-initiatives.index')
            ->with('success', __('Data Initiative deleted successfully.'));
    }

    /**
     * Show governance score history for a data initiative.
     */
    public function showGovernanceScoreHistory(DataInitiative $dataInitiative): View
    {
        $dataInitiative->load(['governanceScoreHistory' => fn ($query) => $query->orderBy('calculated_at', 'desc')]);

        return view('pages.data-initiatives.governance-score-history', compact('dataInitiative'));
    }

    /**
     * Show details for a specific governance score history entry.
     */
    public function showSpecificGovernanceScore(DataInitiative $dataInitiative, DataInitiativeGovernanceScoreHistory $history): View
    {
        abort_unless($history->data_initiative_id === $dataInitiative->id, 404);

        return view('pages.data-initiatives.governance-score-show', compact('dataInitiative', 'history'));
    }
}
