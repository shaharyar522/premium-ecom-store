@extends('layouts.admin')
@php use App\Support\Shop; use App\Models\Order; $title = 'Order '.$order->number; @endphp
@section('actions')
  <a class="btn btn-light" href="{{ route('admin.orders.invoice', $order) }}" target="_blank"><svg class="ico ico-sm"><use href="#a-print"/></svg><span class="hide-sm">Invoice</span></a>
@endsection

@section('content')
<p style="margin:-6px 0 18px"><a class="muted" href="{{ route('admin.orders.index') }}">← All orders</a></p>
<div class="split">
  <div class="stack">
    <div class="panel">
      <div class="panel-head">
        <h2>{{ $order->items->sum('qty') }} items</h2>
        <div style="display:flex;gap:8px"><span class="badge b-{{ $order->status }}">{{ $order->status_label }}</span><span class="badge b-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span></div>
      </div>
      @foreach($order->items as $i)
        <div class="list-row">
          <img class="thumb" src="{{ Shop::img($i->image, 120) }}" alt="">
          <div class="grow">
            <b>{{ $i->name }}</b>
            <div class="muted small">{{ $i->options ? $i->options.' · ' : '' }}{{ Shop::money($i->price) }} × {{ $i->qty }}</div>
            @if($i->product)<a class="small" style="color:var(--accent)" href="{{ route('admin.products.edit', $i->product) }}">Stock now: {{ $i->product->stock }}</a>@endif
          </div>
          <b>{{ Shop::money($i->price * $i->qty) }}</b>
        </div>
      @endforeach
      <div class="panel-body" style="border-top:1px solid var(--line)">
        <dl class="dl" style="grid-template-columns:1fr auto">
          <dt>Subtotal</dt><dd>{{ Shop::money($order->subtotal) }}</dd>
          @if($order->discount)<dt>Discount {{ $order->coupon_code ? '('.$order->coupon_code.')' : '' }}</dt><dd style="color:var(--ok)">−{{ Shop::money($order->discount) }}</dd>@endif
          <dt>Delivery</dt><dd>{{ $order->shipping ? Shop::money($order->shipping) : 'Free' }}</dd>
          <dt style="color:var(--ink);font-weight:800;font-size:15px">Total</dt><dd style="font-size:17px;font-weight:800">{{ Shop::money($order->total) }}</dd>
        </dl>
      </div>
    </div>

    <form class="panel" action="{{ route('admin.orders.update', $order) }}" method="post">
      @csrf @method('PUT')
      <div class="panel-head"><h2>Update order</h2></div>
      <div class="panel-body">
        <div class="grid-3">
          <div class="field"><label for="status">Order status</label>
            <select id="status" name="status">@foreach(Order::STATUSES as $k => $l)<option value="{{ $k }}" @selected($order->status === $k)>{{ $l }}</option>@endforeach</select></div>
          <div class="field"><label for="payment_status">Payment</label>
            <select id="payment_status" name="payment_status">@foreach(['unpaid' => 'Unpaid', 'paid' => 'Paid', 'refunded' => 'Refunded'] as $k => $l)<option value="{{ $k }}" @selected($order->payment_status === $k)>{{ $l }}</option>@endforeach</select></div>
          <div class="field"><label for="tracking_number">Courier tracking #</label><input id="tracking_number" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="e.g. TCS12345678"></div>
        </div>
        <div class="field"><label for="note">Note for the timeline (customer can see it)</label><input id="note" name="note" placeholder="e.g. Handed to TCS, arriving Thursday"></div>
        <p class="help" style="margin:-6px 0 16px">Cancelling or returning an order puts the items back into stock automatically.</p>
        <button class="btn">Save changes</button>
      </div>
    </form>

    <div class="panel">
      <div class="panel-head"><h2>Timeline</h2></div>
      <div class="panel-body">
        <ul class="tl">
          @foreach($order->events->reverse() as $e)
            <li><b>{{ Order::STATUSES[$e->status] ?? ucfirst($e->status) }}</b>@if($e->note) — {{ $e->note }}@endif<div class="muted small">{{ $e->created_at->format('M j, Y · g:i A') }}</div></li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>

  <div class="stack">
    <div class="panel">
      <div class="panel-head"><h2>Customer</h2>@if($order->user)<span class="badge b-ok">Account</span>@endif</div>
      <div class="panel-body">
        <dl class="dl">
          <dt>Name</dt><dd>{{ $order->name }}</dd>
          <dt>Phone</dt><dd><a href="tel:{{ $order->phone }}">{{ $order->phone }}</a></dd>
          @if($order->email)<dt>Email</dt><dd><a href="mailto:{{ $order->email }}">{{ $order->email }}</a></dd>@endif
          <dt>Address</dt><dd>{{ $order->address }}</dd>
          <dt>City</dt><dd>{{ $order->city }}</dd>
          <dt>Payment</dt><dd>{{ $order->payment_method === 'cod' ? 'Cash on delivery' : 'Bank transfer' }}</dd>
          <dt>Placed</dt><dd>{{ $order->created_at->format('M j, Y · g:i A') }}</dd>
        </dl>
        @if($order->notes)<div class="alert" style="background:var(--warn-bg);color:var(--warn);margin:16px 0 0">📝 {{ $order->notes }}</div>@endif
        @php $wa = preg_replace('/\D/', '', $order->phone); if (str_starts_with($wa, '0')) $wa = '92'.substr($wa, 1); @endphp
        <div style="display:flex;gap:8px;margin-top:16px;flex-wrap:wrap">
          <a class="btn btn-sm" style="background:#1FAF5A;border-color:#1FAF5A" target="_blank" rel="noopener"
             href="https://wa.me/{{ $wa }}?text={{ rawurlencode("Hi {$order->name}, this is ".Shop::get('store_name')." about your order {$order->number} (".Shop::money($order->total)."). Can you please confirm your order?") }}">WhatsApp to confirm</a>
          <a class="btn btn-light btn-sm" href="tel:{{ $order->phone }}"><svg class="ico ico-sm"><use href="#a-phone"/></svg>Call</a>
          <a class="btn btn-light btn-sm" href="{{ route('admin.customers.show', $order->phone) }}">Profile</a>
        </div>
      </div>
    </div>
    @if($history->count())
      <div class="panel">
        <div class="panel-head"><h2>Previous orders from this phone</h2></div>
        @foreach($history as $h)
          <a class="list-row" href="{{ route('admin.orders.show', $h) }}">
            <div class="grow"><b>{{ $h->number }}</b><div class="muted small">{{ $h->created_at->format('M j, Y') }}</div></div>
            <span class="badge b-{{ $h->status }}">{{ $h->status_label }}</span>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection
