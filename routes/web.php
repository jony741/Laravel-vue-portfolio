<?php

use App\Http\Controllers\admin\ExperienceController;
use App\Http\Controllers\admin\ProfileController;
use App\Http\Controllers\admin\ProjectController;
use App\Http\Controllers\admin\TechnologyController;
use App\Http\Controllers\admin\TechStackController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.showProfile');
        Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');

        Route::get('/experience', [ExperienceController::class, 'getExperience'])->name('experience.showExperienceData');
        Route::post('/experience', [ExperienceController::class, 'updateExperience'])->name('experience.updateExperience');

        // Project routes
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        Route::get('/technologies', [TechnologyController::class, 'getTechnology'])->name('technologies.getTechnology');
        Route::post('/technologies', [TechnologyController::class, 'store'])->name('technologies.store');
        Route::put('/technologies/{technology}', [TechnologyController::class, 'update'])->name('technologies.update');
        Route::delete('/technologies/{technology}', [TechnologyController::class, 'destroy'])->name('technologies.destroy');

        Route::get('/tech-stack', [TechStackController::class, 'getTechStack'])->name('tech-stack.getTechStack');
        Route::post('/tech-stack', [TechStackController::class, 'updateTechStack'])->name('tech-stack.updateTechStack');
    });

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ProfileController::class, 'index'])->name('profile.index');
});

require __DIR__.'/settings.php';
