@php
  use App\Support\Shop;
  $s = Shop::settings();
  $u = auth()->user();
  $badge = [
    'orders' => \App\Models\Order::where('status', 'pending')->count(),
    'reviews' => \App\Models\Review::where('is_approved', false)->count(),
    'messages' => \App\Models\Message::where('is_read', false)->count(),
  ];
  $nav = [
    'Store' => [
      ['admin.dashboard', 'Dashboard', 'grid', null, 'admin.dashboard'],
      ['admin.orders.index', 'Orders', 'cart', $badge['orders'], 'admin.orders.*'],
      ['admin.products.index', 'Products', 'box', null, 'admin.products.*'],
      ['admin.categories.index', 'Categories', 'tag', null, 'admin.categories.*'],
      ['admin.customers.index', 'Customers', 'users', null, 'admin.customers.*'],
    ],
    'Marketing' => [
      ['admin.coupons.index', 'Coupons', 'ticket', null, 'admin.coupons.*'],
      ['admin.reviews.index', 'Reviews', 'star', $badge['reviews'], 'admin.reviews.*'],
      ['admin.messages.index', 'Messages', 'mail', $badge['messages'], 'admin.messages.*'],
      ['admin.subscribers.index', 'Subscribers', 'send', null, 'admin.subscribers.*'],
    ],
  ];
  if ($u->isAdmin()) $nav['Admin'] = [
    ['admin.settings.edit', 'Settings', 'settings', null, 'admin.settings.*'],
    ['admin.staff.index', 'Staff', 'shield', null, 'admin.staff.*'],
  ];
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title>{{ $title ?? 'Dashboard' }} · {{ $s['store_name'] }} Admin</title>
  <link rel="icon" href="{{ $s['favicon'] ? Shop::img($s['favicon'], 64) : asset('favicon.svg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
  <style>:root{--accent:{{ $s['accent'] }};--accent-rgb:{{ Shop::rgb($s['accent']) }}}</style>
  @stack('head')
</head>
<body>
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <symbol id="a-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></symbol>
  <symbol id="a-cart" viewBox="0 0 24 24"><path d="M5 8h14l-1 12.2a1 1 0 0 1-1 .8H7a1 1 0 0 1-1-.8L5 8z"/><path d="M9 10V7a3 3 0 0 1 6 0v3"/></symbol>
  <symbol id="a-box" viewBox="0 0 24 24"><path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5z"/><path d="m3 7.5 9 4.5 9-4.5M12 12v9"/></symbol>
  <symbol id="a-tag" viewBox="0 0 24 24"><path d="M3 12V4a1 1 0 0 1 1-1h8l9 9-9 9z"/><circle cx="8" cy="8" r="1.5"/></symbol>
  <symbol id="a-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c1-3.5 3.6-5.5 6.5-5.5s5.5 2 6.5 5.5M16 4.5a3.5 3.5 0 0 1 0 7M18 14.5c1.8.6 3 2.4 3.5 5.5"/></symbol>
  <symbol id="a-ticket" viewBox="0 0 24 24"><path d="M3 8a2 2 0 0 0 0 4v0a2 2 0 0 1 0 4v2h18v-2a2 2 0 0 1 0-4 2 2 0 0 1 0-4V6H3z"/><path d="M9 9v.01M9 12v.01M9 15v.01"/></symbol>
  <symbol id="a-star" viewBox="0 0 24 24"><path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/></symbol>
  <symbol id="a-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></symbol>
  <symbol id="a-send" viewBox="0 0 24 24"><path d="M21 3 10 14M21 3l-7 18-4-7-7-4z"/></symbol>
  <symbol id="a-settings" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 0 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 0 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 0 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></symbol>
  <symbol id="a-shield" viewBox="0 0 24 24"><path d="M12 3 4 6v6c0 4.5 3.4 8 8 9 4.6-1 8-4.5 8-9V6z"/></symbol>
  <symbol id="a-external" viewBox="0 0 24 24"><path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/></symbol>
  <symbol id="a-logout" viewBox="0 0 24 24"><path d="M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4M10 16l-4-4 4-4M6 12h11"/></symbol>
  <symbol id="a-menu" viewBox="0 0 24 24"><path d="M3 7h18M3 12h18M3 17h18"/></symbol>
  <symbol id="a-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
  <symbol id="a-download" viewBox="0 0 24 24"><path d="M12 4v11m-5-5 5 5 5-5M5 20h14"/></symbol>
  <symbol id="a-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></symbol>
  <symbol id="a-print" viewBox="0 0 24 24"><path d="M7 9V3h10v6M7 18H4v-7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v7h-3M7 14h10v7H7z"/></symbol>
  <symbol id="a-trash" viewBox="0 0 24 24"><path d="M4 7h16M10 11v6M14 11v6M5 7l1 13h12l1-13M9 7V4h6v3"/></symbol>
  <symbol id="a-copy" viewBox="0 0 24 24"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h3"/></symbol>
  <symbol id="a-phone" viewBox="0 0 24 24"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></symbol>
  <symbol id="a-alert" viewBox="0 0 24 24"><path d="M12 3 2 20h20zM12 10v4M12 17v.01"/></symbol>
</svg>

<div class="app">
  <aside class="side" data-side>
    <a class="brand" href="{{ route('admin.dashboard') }}">
      <span class="brand-mark">{{ mb_substr($s['store_name'], 0, 1) }}</span>
      <span><b>{{ $s['store_name'] }}</b><small>Admin</small></span>
    </a>
    @foreach($nav as $group => $items)
      <div class="side-label">{{ $group }}</div>
      @foreach($items as [$route, $label, $icon, $count, $pattern])
        <a class="nav-item {{ request()->routeIs($pattern) ? 'is-active' : '' }}" href="{{ route($route) }}">
          <svg class="ico"><use href="#a-{{ $icon }}"/></svg>{{ $label }}
          @if($count)<span class="pill">{{ $count }}</span>@endif
        </a>
      @endforeach
    @endforeach
    <div class="side-foot">
      <a class="nav-item" href="{{ route('home') }}" target="_blank"><svg class="ico"><use href="#a-external"/></svg>View store</a>
      <form action="{{ route('admin.logout') }}" method="post">@csrf
        <button class="nav-item" style="all:unset;display:flex;align-items:center;gap:12px;padding:10px 12px;cursor:pointer;width:calc(100% - 24px)"><svg class="ico"><use href="#a-logout"/></svg>Sign out</button>
      </form>
    </div>
  </aside>
  <div class="scrim" data-scrim></div>

  <div class="main">
    <header class="top">
      <button class="icon-btn menu-toggle" data-menu aria-label="Open menu"><svg class="ico"><use href="#a-menu"/></svg></button>
      <h1>{{ $title ?? 'Dashboard' }}</h1>
      <div class="top-actions">
        <form action="{{ route('admin.orders.index') }}" class="hide-sm" style="position:relative">
          <input class="input" name="q" placeholder="Search orders, phone…" style="width:240px;padding-left:36px">
          <svg class="ico ico-sm" style="position:absolute;left:12px;top:12px;color:var(--muted)"><use href="#a-search"/></svg>
        </form>
        @yield('actions')
        <span class="avatar" title="{{ $u->name }} ({{ $u->role }})">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
      </div>
    </header>
    <main class="content">
      @if(session('ok'))<div class="alert alert-ok">{{ session('ok') }}</div>@endif
      @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
      @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
      @yield('content')
    </main>
  </div>
</div>

<script>
  (() => {
    const side = document.querySelector('[data-side]'), scrim = document.querySelector('[data-scrim]');
    const toggle = (on) => { side.classList.toggle('is-open', on); scrim.classList.toggle('is-open', on); };
    document.querySelector('[data-menu]')?.addEventListener('click', () => toggle(true));
    scrim.addEventListener('click', () => toggle(false));
    // Confirm destructive actions.
    document.addEventListener('submit', (e) => { const m = e.target.dataset.confirm; if (m && !confirm(m)) e.preventDefault(); });
    // Select-all checkboxes.
    document.querySelectorAll('[data-check-all]').forEach((all) => all.addEventListener('change', () =>
      document.querySelectorAll(all.dataset.checkAll).forEach((c) => { c.checked = all.checked; })));
    // Image previews for file inputs.
    document.querySelectorAll('input[type=file][data-preview]').forEach((inp) => inp.addEventListener('change', () => {
      const box = document.querySelector(inp.dataset.preview); if (!box) return;
      box.innerHTML = '';
      [...inp.files].forEach((f) => { const i = document.createElement('img'); i.src = URL.createObjectURL(f); i.className = 'thumb'; box.appendChild(i); });
    }));
  })();
</script>
@stack('scripts')
</body>
</html>
