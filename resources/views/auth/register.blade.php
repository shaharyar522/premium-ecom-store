@extends('layouts.store')
@php $title = 'Create account'; @endphp

@section('content')
<section class="section">
  <div class="wrap auth-card">
    <div style="text-align:center;margin-bottom:28px"><span class="eyebrow">Join us</span><h1 class="h1" style="margin-top:12px">Create account</h1><p class="muted">Track orders, save your details and check out faster.</p></div>
    <form class="card-box" action="{{ route('register') }}" method="post">
      @csrf
      <div class="field"><label for="name">Full name</label><input id="name" name="name" value="{{ old('name') }}" required autocomplete="name">@error('name')<span class="error">{{ $message }}</span>@enderror</div>
      <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">@error('email')<span class="error">{{ $message }}</span>@enderror</div>
      <div class="field"><label for="phone">Phone <span class="muted" style="text-transform:none;letter-spacing:0">(optional)</span></label><input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel"></div>
      <div class="row-2">
        <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="new-password">@error('password')<span class="error">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="password_confirmation">Confirm</label><input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"></div>
      </div>
      <button class="btn btn-block">Create account</button>
      <p class="muted small" style="text-align:center;margin:18px 0 0">Already have an account? <a href="{{ route('login') }}" style="text-decoration:underline;color:var(--ink)">Sign in</a></p>
    </form>
  </div>
</section>
@endsection
