<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{

    public function getProject(): Response
    {
        return Inertia::render('admin/project', [
            'profile' => Profile::first(),
        ]);

    }

}
