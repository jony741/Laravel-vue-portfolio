<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon_slug',
        'category',
        'color_hex',
        'icon_link',
    ];

    // Relationship with Projects
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_technologies');
    }

    // Relationship with Stack Items
    public function stackItems()
    {
        return $this->hasMany(StackItem::class);
    }

    // app/Models/Technology.php
    public static function groupedByCategory(): array
    {
        return static::orderBy('category')->orderBy('name')
            ->get()
            ->groupBy('category')
            ->map(fn ($techs) => $techs->values())
            ->toArray();
    }
}
