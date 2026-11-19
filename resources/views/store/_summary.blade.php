@php use App\Support\Shop; @endphp
<aside class="summary">
  <h3>Order summary</h3>
  @if(! empty($mini))
    @foreach($lines as $l)
      <div class="mini-line">
        <div class="thumb"><img src="{{ Shop::img($l->product->image, 140) }}" alt=""><span class="q">{{ $l->qty }}</span></div>
        <div><b>{{ $l->product->name }}</b>@if($l->options)<div class="muted small">{{ $l->options }}</div>@endif</div>
        <span>{{ Shop::money($l->total) }}</span>
      </div>
    @endforeach
    <div style="height:10px"></div>
  @endif

  @if(! empty($showCoupon))
    @if($t['coupon'])
      <form action="{{ route('cart.coupon') }}" method="post" class="sum-row" style="align-items:center">
        @csrf<input type="hidden" name="remove" value="1">
        <span class="status status-delivered">{{ $t['coupon']->code }} applied</span>
        <button class="remove">Remove</button>
      </form>
    @else
      <form action="{{ route('cart.coupon') }}" method="post" class="coupon-form">
        @csrf
        <label class="sr-only" for="coupon">Discount code</label>
        <input id="coupon" name="code" placeholder="Discount code" autocomplete="off">
        <button class="btn btn-sm btn-ghost">Apply</button>
      </form>
    @endif
    @if($t['coupon_error'])<p class="error" style="margin-top:-6px">{{ $t['coupon_error'] }}</p>@endif
  @endif

  <div class="sum-row"><span>Subtotal</span><span>{{ Shop::money($t['subtotal']) }}</span></div>
  @if($t['discount'])<div class="sum-row" style="color:var(--success)"><span>Discount</span><span>−{{ Shop::money($t['discount']) }}</span></div>@endif
  <div class="sum-row"><span>Delivery</span><span>{{ $t['shipping'] ? Shop::money($t['shipping']) : 'Free' }}</span></div>
  @if($t['free_left'] > 0)<p class="muted small" style="margin:4px 0 0">Add {{ Shop::money($t['free_left']) }} more for free delivery.</p>@endif
  <div class="sum-row sum-total"><span>Total</span><span>{{ Shop::money($t['total']) }}</span></div>

  @if(! empty($cta))
    <a href="{{ route('checkout') }}" class="btn btn-block" style="margin-top:18px">Checkout securely</a>
    <p class="muted small" style="text-align:center;margin:12px 0 0">Cash on delivery available nationwide</p>
  @endif
</aside>
