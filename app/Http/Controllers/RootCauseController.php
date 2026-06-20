<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRootCauseRequest;
use App\Http\Requests\UpdateRootCauseRequest;
use App\Models\DataIssue;
use App\Models\RootCause;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RootCauseController extends Controller
{
    /**
     * Display a listing of the root causes.
     */
    public function index(): View
    {
        $rootCauses = RootCause::withCount('dataIssues')
            ->latest()
            ->paginate(10);

        return view('pages.root-causes.index', compact('rootCauses'));
    }

    /**
     * Show the form for creating a new root cause.
     */
    public function create(): View
    {
        $dataIssues = DataIssue::latest()->get();

        return view('pages.root-causes.create', compact('dataIssues'));
    }

    /**
     * Store a newly created root cause in storage.
     */
    public function store(StoreRootCauseRequest $request): RedirectResponse
    {
        $rootCause = RootCause::create($request->validated());

        if ($request->has('data_issue_ids')) {
            $rootCause->dataIssues()->sync($request->input('data_issue_ids'));
        }

        return redirect()
            ->route('web.root-causes.index')
            ->with('success', __('Root cause created successfully.'));
    }

    /**
     * Display the specified root cause.
     */
    public function show(RootCause $rootCause): View
    {
        $rootCause->load(['dataIssues']);

        return view('pages.root-causes.show', compact('rootCause'));
    }

    /**
     * Show the form for editing the specified root cause.
     */
    public function edit(RootCause $rootCause): View
    {
        $dataIssues = DataIssue::latest()->get();

        return view('pages.root-causes.edit', compact('rootCause', 'dataIssues'));
    }

    /**
     * Update the specified root cause in storage.
     */
    public function update(UpdateRootCauseRequest $request, RootCause $rootCause): RedirectResponse
    {
        $rootCause->update($request->validated());

        if ($request->has('data_issue_ids')) {
            $rootCause->dataIssues()->sync($request->input('data_issue_ids'));
        }

        return redirect()
            ->route('web.root-causes.index')
            ->with('success', __('Root cause updated successfully.'));
    }

    /**
     * Remove the specified root cause from storage.
     */
    public function destroy(RootCause $rootCause): RedirectResponse
    {
        $rootCause->delete();

        return redirect()
            ->route('web.root-causes.index')
            ->with('success', __('Root cause deleted successfully.'));
    }
}
