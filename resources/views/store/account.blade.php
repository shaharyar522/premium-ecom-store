@extends('layouts.store')
@php use App\Support\Shop; $title = 'My account'; @endphp

@section('content')
<section class="page-head"><div class="wrap">
  <span class="eyebrow">My account</span>
  <div style="display:flex;justify-content:space-between;align-items:end;gap:16px;flex-wrap:wrap;margin-top:12px">
    <h1 class="h1">Hello, {{ explode(' ', $user->name)[0] }}</h1>
    <form action="{{ route('logout') }}" method="post">@csrf<button class="btn btn-ghost btn-sm">Sign out</button></form>
  </div>
</div></section>

<section class="wrap cart-grid" style="padding-bottom:40px">
  <div>
    <h2 class="h3" style="margin-bottom:16px">Your orders</h2>
    @forelse($orders as $o)
      <a href="{{ route('account.order', $o->number) }}" class="card-box" style="display:flex;justify-content:space-between;align-items:center;gap:14px;margin-bottom:12px;padding:18px 22px">
        <div>
          <b>{{ $o->number }}</b>
          <div class="muted small">{{ $o->created_at->format('M j, Y') }} · {{ $o->items_count }} {{ \Illuminate\Support\Str::plural('item', $o->items_count) }}</div>
        </div>
        <div style="text-align:right">
          <span class="status status-{{ $o->status }}">{{ $o->status_label }}</span>
          <div style="font-weight:700;margin-top:6px">{{ Shop::money($o->total) }}</div>
        </div>
      </a>
    @empty
      <div class="empty card-box"><svg class="ico"><use href="#i-box"/></svg><p class="muted">No orders yet.</p><a class="btn" href="{{ route('shop') }}">Start shopping</a></div>
    @endforelse
  </div>
  <form class="summary" action="{{ route('account.update') }}" method="post">
    @csrf
    <h3>Your details</h3>
    <div class="field"><label for="name">Name</label><input id="name" name="name" value="{{ old('name', $user->name) }}" required></div>
    <div class="field"><label>Email</label><input value="{{ $user->email }}" disabled></div>
    <div class="field"><label for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone', $user->phone) }}"></div>
    <div class="field"><label for="address">Address</label><input id="address" name="address" value="{{ old('address', $user->address) }}"></div>
    <div class="field"><label for="city">City</label><input id="city" name="city" value="{{ old('city', $user->city) }}"></div>
    <details style="margin-bottom:16px"><summary class="small" style="cursor:pointer;font-weight:700">Change password</summary>
      <div class="field" style="margin-top:12px"><label for="pw">New password</label><input id="pw" type="password" name="password" autocomplete="new-password">@error('password')<span class="error">{{ $message }}</span>@enderror</div>
      <div class="field"><label for="pw2">Confirm password</label><input id="pw2" type="password" name="password_confirmation" autocomplete="new-password"></div>
    </details>
    <button class="btn btn-block">Save details</button>
  </form>
</section>
@endsection
