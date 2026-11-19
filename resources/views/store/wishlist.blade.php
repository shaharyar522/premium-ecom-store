@extends('layouts.store')
@php $title = 'Wishlist'; @endphp

@section('content')
<section class="page-head"><div class="wrap"><span class="eyebrow">Saved for later</span><h1 class="h1" style="margin-top:12px">Your wishlist</h1></div></section>
<section class="wrap" style="padding-bottom:40px" data-wishlist-page data-loaded="{{ $loaded ? 1 : 0 }}">
  @if($products->count())
    <div class="grid">@foreach($products as $p) @include('partials.product-card', ['p' => $p]) @endforeach</div>
  @else
    <div class="empty card-box">
      <svg class="ico"><use href="#i-heart"/></svg>
      <h2 class="h3">Nothing saved yet</h2>
      <p class="muted">Tap the heart on any product to save it here.</p>
      <a href="{{ route('shop') }}" class="btn" style="margin-top:8px">Browse products</a>
    </div>
  @endif
</section>
@endsection
