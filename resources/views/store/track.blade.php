@extends('layouts.store')
@php
  use App\Support\Shop;
  use App\Models\Order;
  $title = 'Track your order';
@endphp

@section('content')
<section class="page-head"><div class="wrap" style="max-width:860px">
  @if(! empty($fromAccount))<nav class="crumbs"><a href="{{ route('account') }}">My account</a><span>{{ $order->number }}</span></nav>@endif
  <h1 class="h1">{{ ! empty($fromAccount) ? 'Order '.$order->number : 'Track your order' }}</h1>
</div></section>

<section class="wrap" style="max-width:860px;padding-bottom:40px">
  @if(empty($fromAccount))
  <form class="card-box" action="{{ route('track') }}" style="margin-bottom:24px">
    <div class="row-2">
      <div class="field"><label for="number">Order number</label><input id="number" name="number" value="{{ request('number') }}" placeholder="ORD-123456" required></div>
      <div class="field"><label for="phone">Phone used at checkout</label><input id="phone" name="phone" type="tel" value="{{ request('phone') }}" required></div>
    </div>
    <button class="btn">Track order</button>
  </form>
  @endif

  @if($searched && ! $order)
    <div class="alert alert-error">We couldn't find an order with those details. Please check the order number and phone.</div>
  @endif

  @if($order)
    @php
      $closed = in_array($order->status, ['cancelled', 'returned'], true);
      $pos = array_search($order->status, Order::FLOW, true);
      $when = $order->events->keyBy('status');
      $labels = ['pending' => 'Order placed', 'confirmed' => 'Confirmed', 'processing' => 'Being packed', 'shipped' => 'On its way', 'delivered' => 'Delivered'];
    @endphp
    <div class="card-box">
      <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:24px">
        <div><div class="muted small">Order {{ $order->number }} · {{ $order->created_at->format('M j, Y') }}</div><div class="h3" style="margin-top:4px">{{ Shop::money($order->total) }}</div></div>
        <span class="status status-{{ $order->status }}" style="align-self:flex-start">{{ $order->status_label }}</span>
      </div>
      @if($closed)
        <div class="alert alert-error">This order was {{ $order->status }}. {{ $order->events->last()?->note }}</div>
      @else
        <ol class="timeline">
          @foreach(Order::FLOW as $k => $st)
            <li class="{{ $k < $pos || $order->status === 'delivered' ? 'done' : ($k === $pos ? 'current done' : '') }}">
              <span class="dot">@if($k <= $pos)<svg class="ico"><use href="#i-check"/></svg>@endif</span>
              <b>{{ $labels[$st] }}</b>
              <div class="muted small">{{ isset($when[$st]) ? $when[$st]->created_at->format('M j, g:i A') : 'Pending' }}{{ isset($when[$st]) && $when[$st]->note && $st !== 'pending' ? ' · '.$when[$st]->note : '' }}</div>
              @if($st === 'shipped' && $order->tracking_number && $k <= $pos)<div class="small">Courier tracking: <b>{{ $order->tracking_number }}</b></div>@endif
            </li>
          @endforeach
        </ol>
      @endif
      <div style="border-top:1px solid var(--line);padding-top:12px">
        @foreach($order->items as $i)
          <div class="mini-line">
            <div class="thumb"><img src="{{ Shop::img($i->image, 140) }}" alt=""><span class="q">{{ $i->qty }}</span></div>
            <div><b>{{ $i->name }}</b>@if($i->options)<div class="muted small">{{ $i->options }}</div>@endif</div>
            <span>{{ Shop::money($i->price * $i->qty) }}</span>
          </div>
        @endforeach
        <p class="muted small" style="margin:14px 0 0">Delivering to {{ $order->name }}, {{ $order->address }}, {{ $order->city }}</p>
      </div>
    </div>
    <p style="text-align:center;margin-top:20px"><a class="link" target="_blank" rel="noopener" href="{{ Shop::whatsappUrl("Hi! Question about order {$order->number}") }}">Questions? Chat with us</a></p>
  @endif
</section>
@endsection
