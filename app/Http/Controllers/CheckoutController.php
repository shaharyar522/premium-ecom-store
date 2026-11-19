<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Support\Cart;
use App\Support\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function show()
    {
        $lines = Cart::lines();
        if (! $lines) return redirect()->route('cart');

        return view('store.checkout', ['lines' => $lines, 't' => Cart::totals($lines), 'user' => auth()->user()]);
    }

    public function place(Request $request)
    {
        $methods = array_keys(array_filter(['cod' => Shop::get('cod_enabled'), 'bank' => Shop::get('bank_enabled')]));
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'nullable|email|max:150',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,}$/'],
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:80',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => ['required', 'in:'.implode(',', $methods ?: ['cod'])],
        ]);

        $lines = Cart::lines();
        if (! $lines) return redirect()->route('cart');

        // One format per phone so a customer's orders group together ("0300-111 2223" → "03001112223").
        $data['phone'] = preg_replace('/[^\d+]/', '', $data['phone']);

        $order = DB::transaction(function () use ($data, $lines) {
            // Re-check stock inside the transaction so two buyers can't take the last item.
            foreach ($lines as $l) {
                $stock = Product::whereKey($l->product->id)->lockForUpdate()->value('stock');
                if ($stock < $l->qty) {
                    throw ValidationException::withMessages(['cart' => "Only {$stock} left of {$l->product->name}. Please update your bag."]);
                }
            }

            $t = Cart::totals($lines);
            $order = Order::create($data + [
                'number' => $this->newNumber(),
                'user_id' => auth()->id(),
                'subtotal' => $t['subtotal'],
                'discount' => $t['discount'],
                'shipping' => $t['shipping'],
                'total' => $t['total'],
                'coupon_code' => $t['coupon']?->code,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            foreach ($lines as $l) {
                $order->items()->create([
                    'product_id' => $l->product->id,
                    'name' => $l->product->name,
                    'image' => $l->product->image,
                    'options' => $l->options ?: null,
                    'price' => $l->product->price,
                    'qty' => $l->qty,
                ]);
                Product::whereKey($l->product->id)->update([
                    'stock' => DB::raw('stock - '.(int) $l->qty),
                    'sold' => DB::raw('sold + '.(int) $l->qty),
                ]);
            }
            if ($t['coupon']) Coupon::whereKey($t['coupon']->id)->increment('used');
            $order->events()->create(['status' => 'pending', 'note' => 'Order placed']);

            return $order;
        });

        if ($user = auth()->user()) {
            $user->fill(array_filter(['phone' => $user->phone ?: $data['phone'], 'address' => $user->address ?: $data['address'], 'city' => $user->city ?: $data['city']]))->save();
        }

        Cart::clear();
        session()->push('my_orders', $order->number);

        return redirect()->route('order.success', $order->number);
    }

    public function success(string $number)
    {
        // Guests may only see orders placed in this browser session.
        $order = Order::with('items')->where('number', $number)->firstOrFail();
        abort_unless(in_array($number, session('my_orders', []), true) || ($order->user_id && $order->user_id === auth()->id()), 404);

        return view('store.order-success', ['order' => $order]);
    }

    private function newNumber(): string
    {
        do {
            $n = 'ORD-'.random_int(100000, 999999);
        } while (Order::where('number', $n)->exists());

        return $n;
    }
}
