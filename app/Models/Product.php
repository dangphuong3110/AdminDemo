<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public function categories() : BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_products')->withTimestamps();
    }

    public function manufacturer() : BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function images() : HasMany
    {
        return $this->hasMany(Image::class);
    }
}
