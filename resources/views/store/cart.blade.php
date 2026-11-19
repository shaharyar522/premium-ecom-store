@extends('layouts.store')
@php use App\Support\Shop; $title = 'Your bag'; @endphp

@section('content')
<section class="page-head"><div class="wrap"><h1 class="h1">Your bag</h1></div></section>
<section class="wrap" style="padding-bottom:40px" data-cart-page>
  @if($lines)
  <div class="cart-grid">
    <div class="cart-table">
      @foreach($lines as $l)
        <div class="line">
          <a href="{{ route('product', $l->product) }}"><img src="{{ Shop::img($l->product->image, 240) }}" alt="{{ $l->product->name }}"></a>
          <div>
            <div style="display:flex;justify-content:space-between;gap:12px">
              <a class="line-name" style="font-size:16px" href="{{ route('product', $l->product) }}">{{ $l->product->name }}</a>
              <b>{{ Shop::money($l->total) }}</b>
            </div>
            @if($l->options)<div class="line-meta">{{ $l->options }}</div>@endif
            <div class="line-meta">{{ Shop::money($l->product->price) }} each</div>
            <div class="line-row">
              <div class="stepper">
                <button data-qty="{{ $l->qty - 1 }}" data-key="{{ $l->key }}" aria-label="Decrease">−</button>
                <span>{{ $l->qty }}</span>
                <button data-qty="{{ $l->qty + 1 }}" data-key="{{ $l->key }}" aria-label="Increase">+</button>
              </div>
              <button class="remove" data-qty="0" data-key="{{ $l->key }}">Remove</button>
            </div>
          </div>
        </div>
      @endforeach
      <a href="{{ route('shop') }}" class="link" style="margin-top:24px">← Continue shopping</a>
    </div>
    @include('store._summary', ['showCoupon' => true, 'cta' => true])
  </div>
  @else
    <div class="empty card-box">
      <svg class="ico"><use href="#i-bag"/></svg>
      <h2 class="h3">Your bag is empty</h2>
      <p class="muted">Looks like you haven't added anything yet.</p>
      <a href="{{ route('shop') }}" class="btn" style="margin-top:8px">Start shopping</a>
    </div>
  @endif
</section>
@endsection
