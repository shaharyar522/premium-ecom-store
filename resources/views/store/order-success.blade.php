@extends('layouts.store')
@php use App\Support\Shop; $title = 'Thank you'; @endphp

@section('content')
<section class="section">
  <div class="wrap" style="max-width:760px">
    <div style="text-align:center">
      <div style="width:76px;height:76px;border-radius:50%;background:var(--success);color:#fff;display:grid;place-items:center;margin:0 auto 22px"><svg class="ico" style="width:36px;height:36px;stroke-width:2.4"><use href="#i-check"/></svg></div>
      <span class="eyebrow">Order confirmed</span>
      <h1 class="h1" style="margin:14px 0">Thank you, {{ explode(' ', $order->name)[0] }}!</h1>
      <p class="lead" style="margin:0 auto">Your order <b>{{ $order->number }}</b> has been placed. We'll call or WhatsApp you on <b>{{ $order->phone }}</b> to confirm before dispatch.</p>
    </div>

    <div class="card-box" style="margin-top:36px">
      @foreach($order->items as $i)
        <div class="mini-line">
          <div class="thumb"><img src="{{ Shop::img($i->image, 140) }}" alt=""><span class="q">{{ $i->qty }}</span></div>
          <div><b>{{ $i->name }}</b>@if($i->options)<div class="muted small">{{ $i->options }}</div>@endif</div>
          <span>{{ Shop::money($i->price * $i->qty) }}</span>
        </div>
      @endforeach
      <div style="border-top:1px solid var(--line);margin-top:10px;padding-top:10px">
        <div class="sum-row"><span>Subtotal</span><span>{{ Shop::money($order->subtotal) }}</span></div>
        @if($order->discount)<div class="sum-row"><span>Discount ({{ $order->coupon_code }})</span><span>−{{ Shop::money($order->discount) }}</span></div>@endif
        <div class="sum-row"><span>Delivery</span><span>{{ $order->shipping ? Shop::money($order->shipping) : 'Free' }}</span></div>
        <div class="sum-row sum-total"><span>Total ({{ $order->payment_method === 'cod' ? 'cash on delivery' : 'bank transfer' }})</span><span>{{ Shop::money($order->total) }}</span></div>
      </div>
      @if($order->payment_method === 'bank')
        <div class="alert alert-ok" style="margin:18px 0 0;white-space:pre-line"><b>Bank transfer details</b>
{{ Shop::get('bank_details') }}
Please send the payment receipt on WhatsApp with order number {{ $order->number }}.</div>
      @endif
    </div>

    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:28px">
      <a class="btn" href="{{ route('track', ['number' => $order->number, 'phone' => $order->phone]) }}">Track this order</a>
      <a class="btn btn-whatsapp" target="_blank" rel="noopener" href="{{ Shop::whatsappUrl("Hi! I just placed order {$order->number}.") }}">Message us on WhatsApp</a>
      <a class="btn btn-ghost" href="{{ route('shop') }}">Continue shopping</a>
    </div>
  </div>
</section>
@endsection
