@php use App\Support\Shop; $s = Shop::settings(); @endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoice {{ $order->number }}</title>
  <style>
    body { font: 14px/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; color: #17140F; margin: 0; background: #F5F4F1; }
    .page { max-width: 800px; margin: 24px auto; background: #fff; padding: 48px; border-radius: 12px; }
    .head { display: flex; justify-content: space-between; gap: 20px; flex-wrap: wrap; border-bottom: 2px solid #17140F; padding-bottom: 20px; }
    .logo { font: 600 34px/1 Georgia, serif; }
    h2 { font-size: 12px; letter-spacing: .14em; text-transform: uppercase; color: #6E6A63; margin: 0 0 6px; }
    .cols { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin: 26px 0; }
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; font-size: 11.5px; letter-spacing: .08em; text-transform: uppercase; color: #6E6A63; border-bottom: 1px solid #ddd; padding: 10px 0; }
    td { padding: 12px 0; border-bottom: 1px solid #eee; }
    .r { text-align: right; }
    .tot td { border: 0; padding: 4px 0; }
    .grand td { font-size: 18px; font-weight: 800; border-top: 2px solid #17140F; padding-top: 12px; }
    .foot { margin-top: 36px; font-size: 12.5px; color: #6E6A63; text-align: center; }
    .btn { display: inline-block; padding: 10px 18px; border-radius: 8px; background: #17140F; color: #fff; text-decoration: none; border: 0; font: inherit; cursor: pointer; }
    @media (max-width: 600px) { .page { padding: 24px; margin: 0; border-radius: 0; } .cols { grid-template-columns: 1fr; } }
    @media print { body { background: #fff; } .page { margin: 0; padding: 0; } .no-print { display: none; } }
  </style>
</head>
<body>
<div class="no-print" style="text-align:center;padding:16px"><button class="btn" onclick="print()">Print / Save as PDF</button></div>
<div class="page">
  <div class="head">
    <div><div class="logo">{{ $s['store_name'] }}</div><div style="color:#6E6A63;margin-top:6px">{{ $s['address'] }}<br>{{ $s['phone'] }} · {{ $s['email'] }}</div></div>
    <div class="r"><h2>Invoice</h2><div style="font-size:20px;font-weight:800">{{ $order->number }}</div><div>{{ $order->created_at->format('F j, Y') }}</div></div>
  </div>
  <div class="cols">
    <div><h2>Bill & ship to</h2><b>{{ $order->name }}</b><br>{{ $order->address }}<br>{{ $order->city }}<br>{{ $order->phone }}@if($order->email)<br>{{ $order->email }}@endif</div>
    <div class="r"><h2>Payment</h2>{{ $order->payment_method === 'cod' ? 'Cash on delivery' : 'Bank transfer' }}<br>Status: {{ ucfirst($order->payment_status) }}@if($order->tracking_number)<br>Tracking: {{ $order->tracking_number }}@endif</div>
  </div>
  <table>
    <thead><tr><th>Item</th><th class="r">Price</th><th class="r">Qty</th><th class="r">Total</th></tr></thead>
    <tbody>
      @foreach($order->items as $i)
        <tr><td><b>{{ $i->name }}</b>@if($i->options)<br><span style="color:#6E6A63">{{ $i->options }}</span>@endif</td><td class="r">{{ Shop::money($i->price) }}</td><td class="r">{{ $i->qty }}</td><td class="r">{{ Shop::money($i->price * $i->qty) }}</td></tr>
      @endforeach
    </tbody>
  </table>
  <table style="width:300px;margin:18px 0 0 auto">
    <tr class="tot"><td>Subtotal</td><td class="r">{{ Shop::money($order->subtotal) }}</td></tr>
    @if($order->discount)<tr class="tot"><td>Discount</td><td class="r">−{{ Shop::money($order->discount) }}</td></tr>@endif
    <tr class="tot"><td>Delivery</td><td class="r">{{ $order->shipping ? Shop::money($order->shipping) : 'Free' }}</td></tr>
    <tr class="grand"><td>Total</td><td class="r">{{ Shop::money($order->total) }}</td></tr>
  </table>
  <div class="foot">Thank you for shopping with {{ $s['store_name'] }}! Exchanges within 7 days — keep this invoice.</div>
</div>
</body>
</html>
