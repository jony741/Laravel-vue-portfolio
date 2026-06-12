<?php

use App\Http\Controllers\admin\ExperienceController;
use App\Http\Controllers\admin\ProjectController;
use App\Http\Controllers\admin\TechnologyController;
use App\Http\Controllers\admin\TechStackController;
use App\Http\Controllers\admin\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.showProfile');
        Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');

        Route::get('/experience', [ExperienceController::class, 'getExperience'])->name('experience.showExperienceData');
        Route::post('/experience', [ExperienceController::class, 'updateExperience'])->name('experience.updateExperience');

        Route::get('/projects', [ProjectController::class, 'getProject'])->name('projects.getProject');
        Route::post('/projects', [ProjectController::class, 'updateProject'])->name('projects.updateProject');


        Route::get('/technologies', [TechnologyController::class, 'getTechnology'])->name('technologies.getTechnology');
        Route::post('/technologies', [TechnologyController::class, 'updateTechnology'])->name('technologies.updateTechnology');

        Route::get('/tech-stack', [TechStackController::class, 'getTechStack'])->name('tech-stack.getTechStack');
        Route::post('/tech-stack', [TechStackController::class, 'updateTechStack'])->name('tech-stack.updateTechStack');
    });

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ProfileController::class, 'index'])->name('profile.index');
});

require __DIR__.'/settings.php';
