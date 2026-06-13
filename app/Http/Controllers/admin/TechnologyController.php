<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TechnologyController extends Controller
{
    public function getTechnology(): Response
    {
        return Inertia::render('admin/technology', [
            'technologies' => Technology::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'icon_slug' => ['nullable', 'string', 'max:255'],
            'color_hex' => ['nullable', 'string', 'max:255'],
            'icon_link' => ['nullable', 'string', 'max:255'],
        ]);

        Technology::create($validated);

        return redirect()->route('admin.technologies.getTechnology');
    }

    public function update(Request $request, Technology $technology): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'icon_slug' => ['nullable', 'string', 'max:255'],
            'color_hex' => ['nullable', 'string', 'max:255'],
            'icon_link' => ['nullable', 'string', 'max:255'],
        ]);

        $technology->update($validated);

        return redirect()->route('admin.technologies.getTechnology');
    }

    public function destroy(Technology $technology): RedirectResponse
    {
        $technology->delete();

        return redirect()->route('admin.technologies.getTechnology');
    }
}
