<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Support\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $days = in_array((int) $request->query('days'), [7, 30, 90], true) ? (int) $request->query('days') : 30;
        $from = now()->subDays($days - 1)->startOfDay();
        $prevFrom = (clone $from)->subDays($days);

        // Cancelled / returned orders never count as revenue.
        $valid = fn () => Order::whereNotIn('status', ['cancelled', 'returned']);
        $period = $valid()->where('created_at', '>=', $from);
        $prev = $valid()->whereBetween('created_at', [$prevFrom, $from]);

        $revenue = (int) (clone $period)->sum('total');
        $orders = (clone $period)->count();
        $prevRevenue = (int) (clone $prev)->sum('total');
        $prevOrders = (clone $prev)->count();
        $change = fn ($now, $before) => $before > 0 ? round(($now - $before) * 100 / $before) : null;

        // Daily revenue series for the chart.
        $byDay = (clone $period)->get(['created_at', 'total'])
            ->groupBy(fn ($o) => $o->created_at->format('Y-m-d'))->map->sum('total');
        $series = [];
        for ($d = clone $from; $d <= now(); $d->addDay()) {
            $series[] = ['date' => $d->format('Y-m-d'), 'label' => $d->format('M j'), 'value' => (int) ($byDay[$d->format('Y-m-d')] ?? 0)];
        }

        $top = OrderItem::selectRaw('product_id, name, MAX(image) as image, SUM(qty) as units, SUM(qty*price) as sales')
            ->whereHas('order', fn ($q) => $q->where('created_at', '>=', $from)->whereNotIn('status', ['cancelled', 'returned']))
            ->groupBy('product_id', 'name')->orderByDesc('units')->take(5)->get();

        $statusCounts = Order::selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status');

        return view('admin.dashboard', [
            'days' => $days,
            'kpi' => [
                ['label' => 'Revenue', 'value' => Shop::money($revenue), 'change' => $change($revenue, $prevRevenue)],
                ['label' => 'Orders', 'value' => number_format($orders), 'change' => $change($orders, $prevOrders)],
                ['label' => 'Avg. order value', 'value' => Shop::money($orders ? $revenue / $orders : 0), 'change' => null],
                ['label' => 'New customers', 'value' => number_format(User::where('role', 'customer')->where('created_at', '>=', $from)->count()), 'change' => null],
            ],
            'series' => $series,
            'statusCounts' => $statusCounts,
            'recent' => Order::latest()->take(8)->get(),
            'top' => $top,
            'lowStock' => Product::where('stock', '<=', (int) Shop::get('low_stock', 5))->where('is_active', true)->with('images')->orderBy('stock')->take(6)->get(),
            'pending' => [
                'orders' => $statusCounts['pending'] ?? 0,
                'reviews' => Review::where('is_approved', false)->count(),
                'messages' => Message::where('is_read', false)->count(),
            ],
            'today' => (int) $valid()->where('created_at', '>=', Carbon::today())->sum('total'),
        ]);
    }
}
