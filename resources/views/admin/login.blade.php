@php use App\Support\Shop; $s = Shop::settings(); @endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title>Sign in · {{ $s['store_name'] }} Admin</title>
  <link rel="icon" href="{{ asset('favicon.svg') }}">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,500&family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
  <style>:root{--accent:{{ $s['accent'] }}}</style>
</head>
<body>
<div class="auth">
  <div class="auth-art" style="background-image:url('{{ Shop::img($s['hero_image'], 1200) }}')">
    <div class="quote">“Run your whole store — orders, products and customers — from one calm place.”</div>
  </div>
  <div class="auth-form">
    <form action="{{ route('admin.login') }}" method="post">
      @csrf
      <div class="brand" style="color:var(--ink);padding:0 0 28px">
        <span class="brand-mark" style="color:#fff">{{ mb_substr($s['store_name'], 0, 1) }}</span>
        <span><b>{{ $s['store_name'] }}</b><small style="color:var(--muted)">Admin dashboard</small></span>
      </div>
      <h1 style="font-size:26px;margin:0 0 6px">Welcome back</h1>
      <p class="muted" style="margin:0 0 24px">Sign in with your staff account.</p>
      @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
      <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"></div>
      <div class="field"><label for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password"></div>
      <label class="switch" style="margin-bottom:20px"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
      <button class="btn" style="width:100%;min-height:46px">Sign in</button>
      <p class="muted small" style="text-align:center;margin-top:20px"><a href="{{ route('home') }}">← Back to store</a></p>
    </form>
  </div>
</div>
</body>
</html>
