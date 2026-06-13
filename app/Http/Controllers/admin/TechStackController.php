<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\StackItem;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TechStackController extends Controller
{
    public function getTechStack(): Response
    {
        $profile = Profile::first();

        return Inertia::render('admin/tech-stack', [
            'profile' => $profile,
            'stackItems' => $profile
                ? StackItem::with('technology:id,name,category,color_hex,icon_slug,icon_link')
                    ->where('profile_id', $profile->id)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get()
                : [],
            'technologies' => Technology::orderBy('category')
                ->orderBy('name')
                ->get(['id', 'name', 'category', 'color_hex', 'icon_slug', 'icon_link']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = Profile::first();

        if (! $profile) {
            return back()->withErrors([
                'profile_id' => 'Please create a profile before adding tech stack items.',
            ]);
        }

        $validated = $this->validateStackItem($request, $profile->id);

        StackItem::create([
            ...$validated,
            'profile_id' => $profile->id,
        ]);

        return redirect()->route('admin.tech-stack.getTechStack');
    }

    public function update(Request $request, StackItem $stackItem): RedirectResponse
    {
        $profile = Profile::first();

        if (! $profile) {
            return back()->withErrors([
                'profile_id' => 'Please create a profile before updating tech stack items.',
            ]);
        }

        $validated = $this->validateStackItem($request, $profile->id, $stackItem);

        $stackItem->update([
            ...$validated,
            'profile_id' => $profile->id,
        ]);

        return redirect()->route('admin.tech-stack.getTechStack');
    }

    public function destroy(StackItem $stackItem): RedirectResponse
    {
        $stackItem->delete();

        return redirect()->route('admin.tech-stack.getTechStack');
    }

    private function validateStackItem(Request $request, int $profileId, ?StackItem $stackItem = null): array
    {
        return $request->validate([
            'technology_id' => [
                'required',
                'integer',
                'exists:technologies,id',
                Rule::unique('stack_items', 'technology_id')
                    ->where('profile_id', $profileId)
                    ->ignore($stackItem?->id),
            ],
            'proficiency_level' => ['required', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

    }
}
