@extends('layouts.store')
@php $title = 'Sign in'; @endphp

@section('content')
<section class="section">
  <div class="wrap auth-card">
    <div style="text-align:center;margin-bottom:28px"><span class="eyebrow">Welcome back</span><h1 class="h1" style="margin-top:12px">Sign in</h1></div>
    <form class="card-box" action="{{ route('login') }}" method="post">
      @csrf
      <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">@error('email')<span class="error">{{ $message }}</span>@enderror</div>
      <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password"></div>
      <label class="check" style="margin-bottom:18px"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
      <button class="btn btn-block">Sign in</button>
      <p class="muted small" style="text-align:center;margin:18px 0 0">New here? <a href="{{ route('register') }}" style="text-decoration:underline;color:var(--ink)">Create an account</a></p>
    </form>
  </div>
</section>
@endsection
