<?php

namespace App\Support;

use App\Models\Coupon;
use App\Models\Product;

/**
 * Session cart. Lines are keyed by product + chosen size/colour:
 *   session('cart') = ['12|M|Black' => ['id' => 12, 'size' => 'M', 'color' => 'Black', 'qty' => 2], ...]
 */
class Cart
{
    public static function raw(): array
    {
        return session('cart', []);
    }

    public static function add(Product $p, int $qty = 1, ?string $size = null, ?string $color = null): void
    {
        $cart = self::raw();
        $key = implode('|', [$p->id, $size, $color]);
        $have = $cart[$key]['qty'] ?? 0;
        $cart[$key] = ['id' => $p->id, 'size' => $size, 'color' => $color, 'qty' => max(1, min($have + $qty, $p->stock ?: 1, 20))];
        session(['cart' => $cart]);
    }

    public static function update(string $key, int $qty): void
    {
        $cart = self::raw();
        if (! isset($cart[$key])) return;
        if ($qty <= 0) unset($cart[$key]);
        else {
            $stock = Product::whereKey($cart[$key]['id'])->value('stock') ?: 1;
            $cart[$key]['qty'] = min($qty, $stock, 20);
        }
        session(['cart' => $cart]);
    }

    public static function clear(): void
    {
        session()->forget(['cart', 'coupon']);
    }

    public static function count(): int
    {
        return (int) array_sum(array_column(self::raw(), 'qty'));
    }

    /** Hydrated lines; silently drops products that were deleted or hidden. */
    public static function lines(): array
    {
        $raw = self::raw();
        if (! $raw) return [];
        $products = Product::with('images')->active()->whereIn('id', array_column($raw, 'id'))->get()->keyBy('id');
        $lines = [];
        foreach ($raw as $key => $l) {
            $p = $products[$l['id']] ?? null;
            if (! $p) continue;
            $lines[] = (object) [
                'key' => $key,
                'product' => $p,
                'size' => $l['size'],
                'color' => $l['color'],
                'qty' => $l['qty'],
                'options' => implode(' / ', array_filter([$l['size'], $l['color']])),
                'total' => $p->price * $l['qty'],
            ];
        }

        return $lines;
    }

    public static function coupon(): ?Coupon
    {
        $code = session('coupon');

        return $code ? Coupon::where('code', $code)->first() : null;
    }

    /** subtotal, discount, shipping, total, coupon, coupon_error */
    public static function totals(?array $lines = null): array
    {
        $lines ??= self::lines();
        $subtotal = (int) array_sum(array_map(fn ($l) => $l->total, $lines));

        $coupon = self::coupon();
        $discount = 0;
        $error = null;
        if ($coupon) {
            $error = $coupon->problemFor($subtotal);
            if (! $error) $discount = $coupon->discountFor($subtotal);
        }

        $free = (int) Shop::get('free_shipping_over', 0);
        $shipping = ! $lines || ($free > 0 && $subtotal - $discount >= $free) ? 0 : (int) Shop::get('shipping_fee', 0);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'total' => max(0, $subtotal - $discount + $shipping),
            'coupon' => $error ? null : $coupon,
            'coupon_error' => $error,
            'free_left' => $free > 0 ? max(0, $free - ($subtotal - $discount)) : 0,
        ];
    }
}
