<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/project', [
            'projects' => Project::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'max:255'],
            'live_url' => ['nullable', 'url', 'max:2048'],
            'repo_url' => ['nullable', 'url', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $validated['profile_id'] = Profile::first()->id;
        $validated['is_featured'] = $request->boolean('is_featured');

        Project::create($validated);

        return redirect()->route('admin.projects.index');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'max:255'],
            'live_url' => ['nullable', 'url', 'max:2048'],
            'repo_url' => ['nullable', 'url', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');

        $project->update($validated);

        return redirect()->route('admin.projects.index');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('admin.projects.index');
    }
}
