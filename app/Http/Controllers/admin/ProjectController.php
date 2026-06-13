<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/project', [
            // load technology IDs with each project
            'projects' => Project::with('technologies:id,name,category,color_hex as color')
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($p) => [
                    ...$p->toArray(),
                    'technology_ids' => $p->technologies->pluck('id'),
                ]),

            // grouped technologies for the checkbox UI
            'technologies' => Technology::orderBy('category')
                ->orderBy('name')
                ->get()
                ->groupBy('category')
                ->map(fn ($techs) => $techs->values())
                ->toArray(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {

        $validated = $this->getArr($request);

        $validated['profile_id'] = auth()->user()->profile->id;
        $validated['is_featured'] = $request->boolean('is_featured');

        $project = Project::create($validated);

        // sync pivot table
        if (! empty($validated['technology_ids'])) {
            $project->technologies()->sync($validated['technology_ids']);
        }

        return redirect()->route('admin.projects.index');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $this->getArr($request);
        
        $validated['is_featured'] = $request->boolean('is_featured');

        $project->update($validated);

        // sync replaces all existing pivot records with new selection
        $project->technologies()->sync($validated['technology_ids'] ?? []);

        return redirect()->route('admin.projects.index');
    }

    public function destroy(Project $project):  RedirectResponse
    {
        $project->delete();

        return redirect()->route('admin.projects.index');
    }

    public function getArr(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'max:255'],
            'live_url' => ['nullable', 'url', 'max:2048'],
            'repo_url' => ['nullable', 'url', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'technology_ids' => ['nullable', 'array'],
            'technology_ids.*' => ['exists:technologies,id'],
        ]);
    }
}
