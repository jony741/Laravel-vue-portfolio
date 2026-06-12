<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TechnologyController extends Controller
{
    public function getTechnology(): Response
    {
        return Inertia::render('admin/technology', [
            'profile' => Profile::first(),
        ]);

    }
}
