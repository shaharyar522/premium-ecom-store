<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'expires_at' => 'date'];

    /** Null when usable, otherwise the reason it cannot be used. */
    public function problemFor(int $subtotal): ?string
    {
        if (! $this->is_active) return 'This code is not active.';
        if ($this->expires_at && $this->expires_at->endOfDay()->isPast()) return 'This code has expired.';
        if ($this->max_uses && $this->used >= $this->max_uses) return 'This code has reached its limit.';
        if ($subtotal < $this->min_subtotal) return 'Spend '.\App\Support\Shop::money($this->min_subtotal).' to use this code.';

        return null;
    }

    public function discountFor(int $subtotal): int
    {
        $d = $this->type === 'fixed' ? $this->value : (int) round($subtotal * $this->value / 100);

        return min($d, $subtotal);
    }
}
