<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'icon_path', 'description', 'is_featured'];

    public function getIconUrlAttribute(): ?string
    {
        return $this->icon_path ? '/storage/'.ltrim($this->icon_path, '/') : null;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
