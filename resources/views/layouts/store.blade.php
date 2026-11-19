@php
  use App\Support\Shop;
  use App\Support\Cart;
  $s = Shop::settings();
  $navCats = \App\Models\Category::active()->get();
  $cartCount = Cart::count();
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <script>document.documentElement.classList.add('js')</script>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ trim(($title ?? '') ? $title.' — '.$s['store_name'] : $s['store_name'].' — '.$s['tagline']) }}</title>
  <meta name="description" content="{{ $description ?? $s['meta_description'] }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="{{ $s['dark'] }}">
  <meta property="og:title" content="{{ $title ?? $s['store_name'] }}">
  <meta property="og:description" content="{{ $description ?? $s['meta_description'] }}">
  @isset($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endisset
  <link rel="icon" href="{{ $s['favicon'] ? Shop::img($s['favicon'], 64) : asset('favicon.svg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://images.pexels.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/store.css') }}?v={{ filemtime(public_path('css/store.css')) }}">
  <style>:root{--accent:{{ $s['accent'] }};--accent-rgb:{{ Shop::rgb($s['accent']) }};--ink:{{ $s['dark'] }};--ink-rgb:{{ Shop::rgb($s['dark']) }}}</style>
  @stack('head')
</head>
<body class="{{ $bodyClass ?? '' }}">
@include('partials.icons')
<a class="sr-only" href="#main">Skip to content</a>

@if($s['announcement'])
  <div class="announce">{{ $s['announcement'] }}</div>
@endif

<header class="header" data-header>
  <div class="wrap">
    <div style="display:flex;align-items:center;gap:6px">
      <button class="icon-btn menu-btn" data-open="menu" aria-label="Open menu"><svg class="ico"><use href="#i-menu"/></svg></button>
      <nav class="nav" aria-label="Main">
        <a href="{{ route('shop') }}" class="{{ request()->routeIs('shop') ? 'is-active' : '' }}">Shop all</a>
        @foreach($navCats->take(4) as $c)
          <a href="{{ route('category', $c) }}" class="{{ request()->is('collections/'.$c->slug) ? 'is-active' : '' }}">{{ $c->name }}</a>
        @endforeach
      </nav>
    </div>
    <a href="{{ route('home') }}" class="logo" aria-label="{{ $s['store_name'] }} home">
      @if($s['logo'])<img src="{{ Shop::img($s['logo'], 400) }}" alt="{{ $s['store_name'] }}">@else{{ $s['store_name'] }}@endif
    </a>
    <div class="header-actions">
      <button class="icon-btn" data-open="search" aria-label="Search"><svg class="ico"><use href="#i-search"/></svg></button>
      <a class="icon-btn hide-mobile" href="{{ auth()->check() ? route('account') : route('login') }}" aria-label="Account"><svg class="ico"><use href="#i-user"/></svg></a>
      <a class="icon-btn hide-mobile" href="{{ route('wishlist') }}" data-wishlist-link aria-label="Wishlist"><svg class="ico"><use href="#i-heart"/></svg><span class="count" data-wish-count></span></a>
      <button class="icon-btn" data-open="cart" aria-label="Shopping bag"><svg class="ico"><use href="#i-bag"/></svg><span class="count" data-cart-count data-n="{{ $cartCount }}">{{ $cartCount ?: '' }}</span></button>
    </div>
  </div>
</header>

{{-- Search --}}
<div class="search-panel" data-panel="search" role="dialog" aria-label="Search">
  <div class="wrap">
    <form class="search-form" action="{{ route('shop') }}">
      <svg class="ico"><use href="#i-search"/></svg>
      <input type="search" name="q" placeholder="Search products…" aria-label="Search products" value="{{ request('q') }}">
      <button type="button" class="icon-btn" data-close aria-label="Close search"><svg class="ico"><use href="#i-close"/></svg></button>
    </form>
    <div class="chips">
      @foreach($navCats as $c)<a class="chip" href="{{ route('category', $c) }}">{{ $c->name }}</a>@endforeach
      <a class="chip" href="{{ route('shop', ['sale' => 1]) }}">On sale</a>
    </div>
  </div>
</div>

{{-- Mobile menu --}}
<aside class="drawer drawer-left" data-panel="menu" aria-label="Menu">
  <div class="drawer-head"><span class="logo">{{ $s['store_name'] }}</span><button class="icon-btn" data-close aria-label="Close menu"><svg class="ico"><use href="#i-close"/></svg></button></div>
  <div class="drawer-body mobile-nav">
    <a href="{{ route('shop') }}">Shop all <svg class="ico ico-sm"><use href="#i-chev"/></svg></a>
    @foreach($navCats as $c)<a href="{{ route('category', $c) }}">{{ $c->name }} <svg class="ico ico-sm"><use href="#i-chev"/></svg></a>@endforeach
    <a href="{{ route('shop', ['sale' => 1]) }}">Sale <svg class="ico ico-sm"><use href="#i-chev"/></svg></a>
    <div class="sub" style="margin-top:18px">
      <a href="{{ auth()->check() ? route('account') : route('login') }}">{{ auth()->check() ? 'My account' : 'Sign in' }}</a>
      <a href="{{ route('wishlist') }}" data-wishlist-link>Wishlist</a>
      <a href="{{ route('track') }}">Track order</a>
      <a href="{{ route('contact') }}">Contact</a>
    </div>
  </div>
</aside>

{{-- Cart drawer --}}
<aside class="drawer drawer-right" data-panel="cart" aria-label="Shopping bag">
  <div class="drawer-head"><h3>Your bag</h3><button class="icon-btn" data-close aria-label="Close bag"><svg class="ico"><use href="#i-close"/></svg></button></div>
  <div data-cart-drawer style="display:contents">
    <div class="drawer-body"><div class="empty muted">Loading…</div></div>
  </div>
</aside>
<div class="scrim" data-scrim></div>

<main id="main">
  @if(session('ok') || session('error'))
    <div class="wrap" style="padding-top:18px">
      @if(session('ok'))<div class="alert alert-ok">{{ session('ok') }}</div>@endif
      @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
    </div>
  @endif
  @yield('content')
</main>

<section class="newsletter-band">
  <div class="wrap">
    <span class="eyebrow">Newsletter</span>
    <h2>Join the list</h2>
    <p>New arrivals, private sales and styling notes — straight to your inbox. No spam, ever.</p>
    <form class="newsletter-form" action="{{ route('subscribe') }}" method="post">
      @csrf
      <label class="sr-only" for="nl-email">Email</label>
      <input id="nl-email" type="email" name="email" placeholder="Enter your email" required>
      <button>Subscribe</button>
    </form>
  </div>
</section>

<footer class="footer">
  <div class="wrap">
    <div class="foot-cols">
      <div>
        <div class="foot-logo">{{ $s['store_name'] }}</div>
        <p style="max-width:34ch;margin:18px 0 0;line-height:1.7">{{ $s['tagline'] }}.</p>
        <div class="socials">
          @if($s['instagram'])<a href="{{ $s['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram"><svg class="ico ico-sm"><use href="#i-instagram"/></svg></a>@endif
          @if($s['facebook'])<a href="{{ $s['facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook"><svg class="ico ico-sm"><use href="#i-facebook"/></svg></a>@endif
          @if($s['tiktok'])<a href="{{ $s['tiktok'] }}" target="_blank" rel="noopener" aria-label="TikTok"><svg class="ico ico-sm"><use href="#i-tiktok"/></svg></a>@endif
        </div>
      </div>
      <div>
        <h4>Shop</h4>
        <ul>
          @foreach($navCats as $c)<li><a href="{{ route('category', $c) }}">{{ $c->name }}</a></li>@endforeach
        </ul>
      </div>
      <div>
        <h4>Help</h4>
        <ul>
          <li><a href="{{ route('track') }}">Track your order</a></li>
          <li><a href="{{ route('page', 'shipping') }}">Delivery</a></li>
          <li><a href="{{ route('page', 'returns') }}">Returns & exchanges</a></li>
          <li><a href="{{ route('page', 'faq') }}">FAQ</a></li>
          <li><a href="{{ route('contact') }}">Contact us</a></li>
        </ul>
      </div>
      <div>
        <h4>Get in touch</h4>
        <ul>
          <li><a href="tel:{{ preg_replace('/[^\d+]/', '', $s['phone']) }}">{{ $s['phone'] }}</a></li>
          <li><a href="mailto:{{ $s['email'] }}">{{ $s['email'] }}</a></li>
          <li>{{ $s['address'] }}</li>
          <li><a href="{{ route('page', 'about') }}">About us</a></li>
        </ul>
      </div>
    </div>
    <div class="foot-bottom">
      <span>© {{ date('Y') }} {{ $s['store_name'] }}. All rights reserved. · <a href="{{ route('page', 'privacy') }}">Privacy</a> · <a href="{{ route('page', 'terms') }}">Terms</a></span>
      <div class="pay-badges">@if($s['cod_enabled'])<span>CASH ON DELIVERY</span>@endif @if($s['bank_enabled'])<span>BANK TRANSFER</span>@endif</div>
    </div>
  </div>
</footer>

@if($s['whatsapp'])
<a class="wa-float" href="{{ Shop::whatsappUrl('Hi! I have a question about your store.') }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
  <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.9 11.9 0 0 0 4.6 4c1.7.7 2.4.8 3.2.7a2.8 2.8 0 0 0 1.8-1.3 2.3 2.3 0 0 0 .2-1.3c-.1-.1-.3-.2-.6-.3z"/></svg>
</a>
@endif

<div class="toast" role="status" aria-live="polite" data-toast></div>

<script>
  window.STORE = {
    drawer: @json(route('cart.drawer')),
    update: @json(route('cart.update')),
    wishlist: @json(route('wishlist')),
  };
</script>
<script src="{{ asset('js/store.js') }}?v={{ filemtime(public_path('js/store.js')) }}" defer></script>
@stack('scripts')
</body>
</html>
