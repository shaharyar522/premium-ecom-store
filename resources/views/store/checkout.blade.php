@extends('layouts.store')
@php use App\Support\Shop; $title = 'Checkout'; $s = Shop::settings(); @endphp

@section('content')
<section class="page-head">
  <div class="wrap">
    <div class="steps"><a href="{{ route('cart') }}">Bag</a> › <b>Details</b> › Confirmation</div>
    <h1 class="h1">Checkout</h1>
  </div>
</section>
<section class="wrap" style="padding-bottom:40px">
  <form class="cart-grid" action="{{ route('checkout.place') }}" method="post" novalidate>
    @csrf
    <div>
      @if($errors->any())<div class="alert alert-error">{{ $errors->first('cart') ?: 'Please check the highlighted fields.' }}</div>@endif
      @guest<p class="muted" style="margin-top:0">Have an account? <a href="{{ route('login') }}" style="text-decoration:underline">Sign in</a> for faster checkout — or continue as a guest.</p>@endguest

      <div class="card-box">
        <h2 class="h3" style="margin-bottom:18px">Delivery details</h2>
        <div class="row-2">
          <div class="field"><label for="name">Full name</label><input id="name" name="name" value="{{ old('name', $user?->name) }}" required autocomplete="name">@error('name')<span class="error">{{ $message }}</span>@enderror</div>
          <div class="field"><label for="phone">Phone</label><input id="phone" name="phone" type="tel" value="{{ old('phone', $user?->phone) }}" required autocomplete="tel" placeholder="03xx xxxxxxx">@error('phone')<span class="error">Please enter a valid phone number.</span>@enderror</div>
        </div>
        <div class="field"><label for="email">Email <span class="muted" style="text-transform:none;letter-spacing:0">(optional, for your receipt)</span></label><input id="email" name="email" type="email" value="{{ old('email', $user?->email) }}" autocomplete="email">@error('email')<span class="error">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="address">Address</label><input id="address" name="address" value="{{ old('address', $user?->address) }}" required autocomplete="street-address" placeholder="House, street, area">@error('address')<span class="error">{{ $message }}</span>@enderror</div>
        <div class="row-2">
          <div class="field"><label for="city">City</label><input id="city" name="city" value="{{ old('city', $user?->city) }}" required autocomplete="address-level2">@error('city')<span class="error">{{ $message }}</span>@enderror</div>
          <div class="field"><label for="notes">Order note <span class="muted" style="text-transform:none;letter-spacing:0">(optional)</span></label><input id="notes" name="notes" value="{{ old('notes') }}" placeholder="e.g. call before delivery"></div>
        </div>
      </div>

      <div class="card-box" style="margin-top:18px">
        <h2 class="h3" style="margin-bottom:18px">Payment</h2>
        @if($s['cod_enabled'])
          <label class="radio-card"><input type="radio" name="payment_method" value="cod" @checked(old('payment_method', 'cod') === 'cod')>
            <span><b>Cash on delivery</b><small>Pay in cash when your order arrives.</small></span></label>
        @endif
        @if($s['bank_enabled'])
          <label class="radio-card"><input type="radio" name="payment_method" value="bank" @checked(old('payment_method') === 'bank' || ! $s['cod_enabled'])>
            <span><b>Bank transfer</b><small>{{ $s['bank_details'] }}
Send the receipt on WhatsApp with your order number.</small></span></label>
        @endif
        @error('payment_method')<span class="error">Please choose a payment method.</span>@enderror
      </div>
    </div>

    <div>
      @include('store._summary', ['mini' => true])
      <button class="btn btn-block" style="margin-top:14px">Place order · {{ Shop::money($t['total']) }}</button>
      <p class="muted small" style="text-align:center;margin-top:12px"><svg class="ico ico-sm" style="vertical-align:-4px"><use href="#i-shield"/></svg> Your details are only used to deliver your order.</p>
    </div>
  </form>
</section>
@endsection
