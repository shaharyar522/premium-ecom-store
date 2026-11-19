@php use App\Support\Shop; @endphp
<div class="drawer-body">
  @forelse($lines as $l)
    <div class="line">
      <a href="{{ route('product', $l->product) }}"><img src="{{ Shop::img($l->product->image, 200) }}" alt="{{ $l->product->name }}" loading="lazy"></a>
      <div>
        <a class="line-name" href="{{ route('product', $l->product) }}">{{ $l->product->name }}</a>
        @if($l->options)<div class="line-meta">{{ $l->options }}</div>@endif
        <div class="line-meta">{{ Shop::money($l->product->price) }}</div>
        <div class="line-row">
          <div class="stepper">
            <button data-qty="{{ $l->qty - 1 }}" data-key="{{ $l->key }}" aria-label="Decrease">−</button>
            <span>{{ $l->qty }}</span>
            <button data-qty="{{ $l->qty + 1 }}" data-key="{{ $l->key }}" aria-label="Increase">+</button>
          </div>
          <b>{{ Shop::money($l->total) }}</b>
        </div>
      </div>
    </div>
  @empty
    <div class="empty">
      <svg class="ico"><use href="#i-bag"/></svg>
      <h3 class="h3">Your bag is empty</h3>
      <p class="muted">Discover something you'll love.</p>
      <a href="{{ route('shop') }}" class="btn" style="margin-top:10px">Start shopping</a>
    </div>
  @endforelse
</div>
@if($lines)
  <div class="drawer-foot">
    @if($t['free_left'] > 0)
      <div class="ship-meter">Add <b>{{ Shop::money($t['free_left']) }}</b> more for free delivery</div>
    @else
      <div class="ship-meter">🎉 You've unlocked <b>free delivery</b></div>
    @endif
    @php $free = (int) Shop::get('free_shipping_over'); @endphp
    @if($free > 0)<div class="meter"><i style="width:{{ min(100, round(($t['subtotal'] - $t['discount']) * 100 / $free)) }}%"></i></div>@endif
    <div class="sum-row" style="font-weight:700;font-size:16px"><span>Subtotal</span><span>{{ Shop::money($t['subtotal']) }}</span></div>
    <p class="muted small" style="margin:2px 0 14px">Delivery and discount codes are applied at checkout.</p>
    <a href="{{ route('checkout') }}" class="btn btn-block">Checkout</a>
    <a href="{{ route('cart') }}" class="btn btn-ghost btn-block" style="margin-top:8px">View bag</a>
  </div>
@endif
