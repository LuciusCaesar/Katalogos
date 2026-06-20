<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSolutionRequest;
use App\Http\Requests\UpdateSolutionRequest;
use App\Models\RootCause;
use App\Models\Solution;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SolutionController extends Controller
{
    /**
     * Display a listing of the solutions.
     */
    public function index(): View
    {
        $solutions = Solution::withCount('rootCauses')
            ->latest()
            ->paginate(10);

        return view('pages.solutions.index', compact('solutions'));
    }

    /**
     * Show the form for creating a new solution.
     */
    public function create(): View
    {
        $rootCauses = RootCause::latest()->get();

        return view('pages.solutions.create', compact('rootCauses'));
    }

    /**
     * Store a newly created solution in storage.
     */
    public function store(StoreSolutionRequest $request): RedirectResponse
    {
        $solution = Solution::create($request->validated());

        if ($request->has('root_cause_ids')) {
            $solution->rootCauses()->sync($request->input('root_cause_ids'));
        }

        return redirect()
            ->route('web.solutions.index')
            ->with('success', __('Solution created successfully.'));
    }

    /**
     * Display the specified solution.
     */
    public function show(Solution $solution): View
    {
        $solution->load(['rootCauses']);

        return view('pages.solutions.show', compact('solution'));
    }

    /**
     * Show the form for editing the specified solution.
     */
    public function edit(Solution $solution): View
    {
        $rootCauses = RootCause::latest()->get();

        return view('pages.solutions.edit', compact('solution', 'rootCauses'));
    }

    /**
     * Update the specified solution in storage.
     */
    public function update(UpdateSolutionRequest $request, Solution $solution): RedirectResponse
    {
        $solution->update($request->validated());

        if ($request->has('root_cause_ids')) {
            $solution->rootCauses()->sync($request->input('root_cause_ids'));
        }

        return redirect()
            ->route('web.solutions.index')
            ->with('success', __('Solution updated successfully.'));
    }

    /**
     * Remove the specified solution from storage.
     */
    public function destroy(Solution $solution): RedirectResponse
    {
        $solution->delete();

        return redirect()
            ->route('web.solutions.index')
            ->with('success', __('Solution deleted successfully.'));
    }
}
