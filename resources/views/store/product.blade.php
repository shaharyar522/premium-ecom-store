@extends('layouts.store')
@php
  use App\Support\Shop;
  $title = $product->name;
  $description = $product->short_description;
  $ogImage = Shop::img($product->image, 1200);
  $imgs = $product->images->count() ? $product->images : collect([(object) ['path' => null]]);
  $low = (int) Shop::get('low_stock', 5);
@endphp
@push('head')
<script type="application/ld+json">{!! json_encode([
  '@context' => 'https://schema.org', '@type' => 'Product', 'name' => $product->name, 'image' => $ogImage,
  'description' => $product->short_description, 'sku' => $product->sku,
  'offers' => ['@type' => 'Offer', 'price' => $product->price, 'priceCurrency' => 'PKR', 'availability' => $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'],
] + ($rating ? ['aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => $rating, 'reviewCount' => $product->approvedReviews->count()]] : []), JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
<section class="wrap" style="padding-top:22px">
  <nav class="crumbs" aria-label="Breadcrumb">
    <a href="{{ route('home') }}">Home</a>
    @if($product->category)<span><a href="{{ route('category', $product->category) }}">{{ $product->category->name }}</a></span>@endif
    <span>{{ $product->name }}</span>
  </nav>

  <div class="pdp">
    <div class="gallery {{ $imgs->count() < 2 ? 'single' : '' }}">
      <div class="thumbs">
        @foreach($imgs as $i => $img)
          <button data-thumb class="{{ $i === 0 ? 'is-active' : '' }}" aria-label="Image {{ $i + 1 }}"><img src="{{ Shop::img($img->path, 160) }}" alt=""></button>
        @endforeach
      </div>
      <div class="stage" data-stage>
        <div class="stage-track">
          @foreach($imgs as $i => $img)
            <img src="{{ Shop::img($img->path, 1200) }}" alt="{{ $product->name }}{{ $i ? ' — view '.($i + 1) : '' }}" @if($i) loading="lazy" @else fetchpriority="high" @endif>
          @endforeach
        </div>
        @if($imgs->count() > 1)<div class="dots">@foreach($imgs as $i => $img)<i class="{{ $i === 0 ? 'is-active' : '' }}"></i>@endforeach</div>@endif
        <div class="badges">
          @if($product->discount_percent)<span class="badge badge-sale">−{{ $product->discount_percent }}%</span>@endif
          @if($product->badge && $product->badge !== 'Sale')<span class="badge">{{ $product->badge }}</span>@endif
        </div>
      </div>
    </div>

    <div class="pdp-info">
      @if($product->category)<span class="eyebrow">{{ $product->category->name }}</span>@endif
      <h1>{{ $product->name }}</h1>
      @if($rating)
        <a href="#reviews" style="display:inline-flex;gap:8px;align-items:center;font-size:13px">@include('partials.stars', ['rating' => $rating]) <span class="muted">{{ $rating }} · {{ $product->approvedReviews->count() }} reviews</span></a>
      @endif
      <div class="price pdp-price">
        <span>{{ Shop::money($product->price) }}</span>
        @if($product->discount_percent)<s>{{ Shop::money($product->compare_price) }}</s><span class="off">Save {{ Shop::money($product->compare_price - $product->price) }}</span>@endif
      </div>
      @if($product->short_description)<p class="lead">{{ $product->short_description }}</p>@endif

      @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

      <form action="{{ route('cart.add', $product) }}" method="post" data-ajax-cart id="buy-form">
        @csrf
        @if($product->colors)
          <div class="opt">
            <div class="opt-label">Colour <span data-opt-out="color">{{ $product->colors[0] }}</span></div>
            <div class="pills" data-opt="color">
              @foreach($product->colors as $i => $c)
                <input type="radio" name="color" id="c{{ $i }}" value="{{ $c }}" @checked($i === 0)><label for="c{{ $i }}">{{ $c }}</label>
              @endforeach
            </div>
          </div>
        @endif
        @if($product->sizes)
          <div class="opt">
            <div class="opt-label">Size <span data-opt-out="size">Select a size</span></div>
            <div class="pills" data-opt="size">
              @foreach($product->sizes as $i => $sz)
                <input type="radio" name="size" id="s{{ $i }}" value="{{ $sz }}" required><label for="s{{ $i }}">{{ $sz }}</label>
              @endforeach
            </div>
          </div>
        @endif

        <div style="margin-top:6px">
          @if(! $product->in_stock)<span class="stock out">Sold out — back soon</span>
          @elseif($product->stock <= $low)<span class="stock low">Only {{ $product->stock }} left — order soon</span>
          @else<span class="stock">In stock, ready to ship</span>@endif
        </div>

        <div class="buy-row" data-main-buy>
          <div class="stepper">
            <button type="button" data-step="#qty" data-d="-1" aria-label="Decrease quantity">−</button>
            <input id="qty" name="qty" type="number" value="1" min="1" max="{{ max(1, min(20, $product->stock)) }}" aria-label="Quantity" inputmode="numeric">
            <button type="button" data-step="#qty" data-d="1" aria-label="Increase quantity">+</button>
          </div>
          <button class="btn" @disabled(! $product->in_stock)>Add to bag</button>
          <button type="button" class="icon-btn" data-wish="{{ $product->id }}" aria-label="Save to wishlist" style="border:1px solid var(--line);width:50px;height:50px"><svg class="ico"><use href="#i-heart"/></svg></button>
        </div>
        @if($product->in_stock)
          <button class="btn btn-accent btn-block" name="buy_now" value="1" style="margin-top:10px">Buy it now</button>
        @endif
      </form>
      <a class="btn btn-whatsapp btn-block" style="margin-top:10px" target="_blank" rel="noopener"
         href="{{ Shop::whatsappUrl("Hi! I'd like to order: {$product->name} (".Shop::money($product->price).") — ".route('product', $product)) }}">
        Order on WhatsApp
      </a>

      <div class="perks">
        <div class="perk"><svg class="ico"><use href="#i-truck"/></svg>Delivery in 2–4 days</div>
        <div class="perk"><svg class="ico"><use href="#i-cash"/></svg>Cash on delivery</div>
        <div class="perk"><svg class="ico"><use href="#i-refresh"/></svg>7-day exchange</div>
        <div class="perk"><svg class="ico"><use href="#i-shield"/></svg>Quality guaranteed</div>
      </div>

      <div class="accordion">
        <details open><summary>Details</summary><div class="acc-body">{{ $product->description ?: $product->short_description }}</div></details>
        <details><summary>Delivery & returns</summary><div class="acc-body">Free delivery on orders over {{ Shop::money(Shop::get('free_shipping_over')) }}, otherwise {{ Shop::money(Shop::get('shipping_fee')) }}.
Orders are dispatched within 24 hours and arrive in 2–4 working days.
Exchanges are free within 7 days for unworn items with tags attached.</div></details>
        @if($product->sku)<details><summary>Product code</summary><div class="acc-body">{{ $product->sku }}</div></details>@endif
      </div>
    </div>
  </div>
</section>

<section class="section" id="reviews">
  <div class="wrap reviews-wrap">
    <div>
      <span class="eyebrow">Reviews</span>
      <div class="rating-big" style="margin-top:14px">{{ $rating ?: '—' }}</div>
      @include('partials.stars', ['rating' => $rating])
      <p class="muted small">Based on {{ $product->approvedReviews->count() }} reviews</p>
      <details class="card-box" style="margin-top:20px">
        <summary class="btn btn-ghost btn-block" style="list-style:none">Write a review</summary>
        <form action="{{ route('review', $product) }}" method="post" style="margin-top:18px">
          @csrf
          <div class="field"><label for="rv-name">Your name</label><input id="rv-name" name="name" required maxlength="80"></div>
          <div class="field"><label for="rv-rating">Rating</label>
            <select id="rv-rating" name="rating">@for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ str_repeat('★', $i) }} ({{ $i }})</option>@endfor</select></div>
          <div class="field"><label for="rv-body">Review</label><textarea id="rv-body" name="body" required maxlength="1500"></textarea></div>
          <button class="btn btn-block">Submit review</button>
        </form>
      </details>
    </div>
    <div class="review-list">
      @forelse($product->approvedReviews->take(8) as $r)
        <div class="review-item">
          <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap">@include('partials.stars', ['rating' => $r->rating])<span class="muted small">{{ $r->created_at->diffForHumans() }}</span></div>
          <p style="margin:10px 0 6px">{{ $r->body }}</p>
          <b class="small">{{ $r->name }}</b> <span class="muted small">· Verified buyer</span>
        </div>
      @empty
        <div class="review-item muted">No reviews yet — be the first to share your thoughts.</div>
      @endforelse
    </div>
  </div>
</section>

@if($related->count())
<section class="section" style="padding-top:0">
  <div class="wrap">
    <div class="section-head"><div><span class="eyebrow">You may also like</span><h2 class="h2">Complete the look</h2></div></div>
    <div class="grid">@foreach($related as $p) @include('partials.product-card', ['p' => $p]) @endforeach</div>
  </div>
</section>
@endif

@if($product->in_stock)
<div class="sticky-buy" data-sticky-buy>
  <div style="min-width:0"><div class="small" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:700">{{ $product->name }}</div><div class="small muted">{{ Shop::money($product->price) }}</div></div>
  <button class="btn" form="buy-form">Add to bag</button>
</div>
@endif
@endsection
