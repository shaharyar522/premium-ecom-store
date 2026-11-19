<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private function filtered(Request $request)
    {
        $q = Order::query()->withCount('items');
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('number', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        if ($st = $request->query('status')) $q->where('status', $st);
        if ($pay = $request->query('payment')) $q->where('payment_status', $pay);
        if ($from = $request->query('from')) $q->whereDate('created_at', '>=', $from);
        if ($to = $request->query('to')) $q->whereDate('created_at', '<=', $to);

        return $q->latest();
    }

    public function index(Request $request)
    {
        return view('admin.orders.index', [
            'orders' => $this->filtered($request)->paginate(20)->withQueryString(),
            'counts' => Order::selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'events', 'user');

        return view('admin.orders.show', [
            'order' => $order,
            'history' => Order::where('phone', $order->phone)->where('id', '!=', $order->id)->latest()->take(5)->get(),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Order::STATUSES)),
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'tracking_number' => 'nullable|string|max:60',
            'note' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($order, $data) {
            $wasOpen = ! in_array($order->status, ['cancelled', 'returned'], true);
            $nowClosed = in_array($data['status'], ['cancelled', 'returned'], true);

            // Put stock back when an order is cancelled/returned (and take it again if re-opened).
            if ($wasOpen !== ! $nowClosed) {
                foreach ($order->items as $i) {
                    if (! $i->product_id) continue;
                    $sign = $nowClosed ? '+' : '-';
                    Product::whereKey($i->product_id)->update([
                        'stock' => DB::raw("stock {$sign} ".(int) $i->qty),
                        'sold' => DB::raw('sold '.($nowClosed ? '-' : '+').' '.(int) $i->qty),
                    ]);
                }
            }

            $note = $data['note'] ?? null;
            $order->update(['payment_status' => $data['payment_status'], 'tracking_number' => $data['tracking_number'] ?? null]);
            if ($data['status'] !== $order->status || $note) {
                $order->setStatus($data['status'], $note ?: null);
            }
        });

        return back()->with('ok', "Order {$order->number} updated.");
    }

    public function bulk(Request $request)
    {
        $data = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer', 'status' => 'required|in:confirmed,processing,shipped,delivered']);
        $orders = Order::whereIn('id', $data['ids'])->whereNotIn('status', ['cancelled', 'returned'])->get();
        foreach ($orders as $o) {
            if ($o->status !== $data['status']) $o->setStatus($data['status'], 'Bulk update');
        }

        return back()->with('ok', $orders->count().' orders marked '.Order::STATUSES[$data['status']].'.');
    }

    public function invoice(Order $order)
    {
        return view('admin.orders.invoice', ['order' => $order->load('items')]);
    }

    public function export(Request $request)
    {
        $rows = $this->filtered($request)->with('items')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order', 'Date', 'Customer', 'Phone', 'Email', 'City', 'Address', 'Items', 'Subtotal', 'Discount', 'Shipping', 'Total', 'Payment', 'Payment status', 'Status', 'Tracking']);
            foreach ($rows as $o) {
                fputcsv($out, [$o->number, $o->created_at->format('Y-m-d H:i'), $o->name, $o->phone, $o->email, $o->city, $o->address,
                    $o->items->map(fn ($i) => "{$i->qty} x {$i->name}".($i->options ? " ({$i->options})" : ''))->implode('; '),
                    $o->subtotal, $o->discount, $o->shipping, $o->total, strtoupper($o->payment_method), $o->payment_status, $o->status_label, $o->tracking_number]);
            }
            fclose($out);
        }, 'orders-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }
}
