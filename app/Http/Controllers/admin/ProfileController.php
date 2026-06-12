<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/profile', [
            'profile' => Profile::first(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'experience_summary' => 'nullable|string',
            'avatar_url' => 'nullable|string|url',
            'portfolio_github_folder_link' => 'nullable|string|url',
            'linked_link' => 'nullable|string|url',
            'email' => 'nullable|email',
            'is_active' => 'boolean',
        ]);
        $profile = Profile::first() ?? new Profile;
        $profile->fill($validated);
        $profile->is_active = isset($validated['is_active']) ? 1 : 0;
        $profile->save();

        return redirect()->back()->with('success', 'Profile saved successfully!');
    }

    public function destroy(Profile $profile)
    {
        $profile->delete();

        return redirect()->back()->with('success', 'Profile deleted successfully!');
    }
}
