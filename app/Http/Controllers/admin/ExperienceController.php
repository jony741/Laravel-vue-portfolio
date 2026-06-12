<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Inertia\Inertia;
use Inertia\Response;

class ExperienceController extends Controller
{
    public function getExperience(): Response
    {
        return Inertia::render('admin/experience', [
            'profile' => Profile::first(),
        ]);

    }
}
