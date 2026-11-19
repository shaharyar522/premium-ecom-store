<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $lines = Cart::lines();

        return view('store.cart', ['lines' => $lines, 't' => Cart::totals($lines)]);
    }

    /** Slide-out cart contents (HTML) + item count, for the header drawer. */
    public function drawer()
    {
        $lines = Cart::lines();

        return response()->json([
            'count' => Cart::count(),
            'html' => view('partials.cart-drawer', ['lines' => $lines, 't' => Cart::totals($lines)])->render(),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);
        $data = $request->validate([
            'qty' => 'nullable|integer|min:1|max:20',
            'size' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:30',
        ]);

        $error = null;
        if (! $product->in_stock) $error = 'Sorry, this item is sold out.';
        elseif ($product->sizes && empty($data['size'])) $error = 'Please choose a size.';
        elseif ($product->sizes && ! in_array($data['size'], $product->sizes, true)) $error = 'Please choose a valid size.';

        if (! $error) {
            $color = $data['color'] ?? ($product->colors[0] ?? null);
            Cart::add($product, (int) ($data['qty'] ?? 1), $data['size'] ?? null, $color);
        }

        if ($request->expectsJson()) {
            return $error ? response()->json(['message' => $error], 422) : $this->drawer();
        }
        if ($error) return back()->with('error', $error);

        return $request->boolean('buy_now') ? redirect()->route('checkout') : redirect()->route('cart')->with('ok', 'Added to your bag.');
    }

    public function update(Request $request)
    {
        $data = $request->validate(['key' => 'required|string', 'qty' => 'required|integer|min:0|max:20']);
        Cart::update($data['key'], (int) $data['qty']);

        return $request->expectsJson() ? $this->drawer() : back();
    }

    public function coupon(Request $request)
    {
        if ($request->boolean('remove')) {
            session()->forget('coupon');

            return back()->with('ok', 'Code removed.');
        }

        $code = strtoupper(trim((string) $request->input('code')));
        $coupon = $code ? Coupon::where('code', $code)->first() : null;
        if (! $coupon) return back()->with('error', 'That code is not valid.');
        if ($problem = $coupon->problemFor(Cart::totals()['subtotal'])) return back()->with('error', $problem);

        session(['coupon' => $coupon->code]);

        return back()->with('ok', "Code {$coupon->code} applied.");
    }
}
