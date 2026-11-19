@extends('layouts.store')
@php use App\Support\Shop; $title = 'Contact us'; $s = Shop::settings(); @endphp

@section('content')
<section class="page-head"><div class="wrap"><span class="eyebrow">We're here to help</span><h1 class="h1" style="margin-top:12px">Contact us</h1></div></section>
<section class="wrap cart-grid" style="padding-bottom:40px">
  <form class="card-box" action="{{ route('contact.send') }}" method="post">
    @csrf
    <div class="row-2">
      <div class="field"><label for="name">Name</label><input id="name" name="name" value="{{ old('name') }}" required>@error('name')<span class="error">{{ $message }}</span>@enderror</div>
      <div class="field"><label for="phone">Phone</label><input id="phone" name="phone" type="tel" value="{{ old('phone') }}"></div>
    </div>
    <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}">@error('email')<span class="error">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="body">Message</label><textarea id="body" name="body" required>{{ old('body') }}</textarea>@error('body')<span class="error">{{ $message }}</span>@enderror</div>
    <button class="btn">Send message</button>
  </form>
  <aside class="summary">
    <h3>Reach us directly</h3>
    <div class="perk" style="margin-bottom:10px"><svg class="ico"><use href="#i-phone"/></svg><a href="tel:{{ preg_replace('/[^\d+]/', '', $s['phone']) }}">{{ $s['phone'] }}</a></div>
    <div class="perk" style="margin-bottom:10px"><svg class="ico"><use href="#i-mail"/></svg><a href="mailto:{{ $s['email'] }}">{{ $s['email'] }}</a></div>
    <div class="perk" style="margin-bottom:18px"><svg class="ico"><use href="#i-pin"/></svg>{{ $s['address'] }}</div>
    <a class="btn btn-whatsapp btn-block" target="_blank" rel="noopener" href="{{ Shop::whatsappUrl('Hi!') }}">Chat on WhatsApp</a>
    <p class="muted small" style="margin:14px 0 0">Mon–Sat, 10am–8pm. We usually reply within an hour.</p>
  </aside>
</section>
@endsection
