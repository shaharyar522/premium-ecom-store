<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Customers = everyone who has ordered, grouped by phone number, so cash-on-delivery
     * shoppers who never created an account still show up.
     */
    public function index(Request $request)
    {
        $q = Order::selectRaw("phone, MAX(name) as name, MAX(email) as email, MAX(city) as city, MAX(user_id) as user_id, COUNT(*) as orders, SUM(CASE WHEN status NOT IN ('cancelled','returned') THEN total ELSE 0 END) as spent, MAX(created_at) as last_order")
            ->groupBy('phone');
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        $sort = $request->query('sort') === 'recent' ? 'last_order' : 'spent';

        return view('admin.customers.index', [
            'customers' => $q->orderByDesc($sort)->paginate(25)->withQueryString(),
            'accounts' => User::where('role', 'customer')->count(),
        ]);
    }

    public function show(string $customer)
    {
        $orders = Order::withCount('items')->where('phone', $customer)->latest()->get();
        abort_if($orders->isEmpty(), 404);

        return view('admin.customers.show', [
            'phone' => $customer,
            'orders' => $orders,
            'latest' => $orders->first(),
            'account' => User::find($orders->whereNotNull('user_id')->first()?->user_id),
            'spent' => $orders->whereNotIn('status', ['cancelled', 'returned'])->sum('total'),
        ]);
    }
}
