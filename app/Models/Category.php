<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
