<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sizes' => 'array',
        'colors' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true)->latest();
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function getImageAttribute(): ?string
    {
        return $this->images->first()?->path;
    }

    public function getHoverImageAttribute(): ?string
    {
        return $this->images->get(1)?->path;
    }

    public function getDiscountPercentAttribute(): int
    {
        return $this->compare_price && $this->compare_price > $this->price
            ? (int) round(100 - $this->price * 100 / $this->compare_price) : 0;
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }
}
