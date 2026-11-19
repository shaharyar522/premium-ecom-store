@extends('layouts.store')
@php
  use App\Support\Shop;
  $s = Shop::settings();
  $ogImage = Shop::img($s['hero_image'], 1200);
  // Up to 3 hero slides; a slide without a title is skipped.
  $slides = collect([
    ['img' => $s['hero_image'], 'eyebrow' => $s['hero_eyebrow'], 'title' => $s['hero_title'], 'text' => $s['hero_subtitle'], 'url' => route('shop')],
    ['img' => $s['hero_image_2'] ?? null, 'eyebrow' => $s['hero2_eyebrow'] ?? null, 'title' => $s['hero2_title'] ?? null, 'text' => $s['hero2_subtitle'] ?? null, 'url' => route('shop', ['sale' => 1])],
    ['img' => $s['hero_image_3'] ?? null, 'eyebrow' => $s['hero3_eyebrow'] ?? null, 'title' => $s['hero3_title'] ?? null, 'text' => $s['hero3_subtitle'] ?? null, 'url' => route('shop', ['sort' => 'popular'])],
  ])->filter(fn ($sl) => $sl['img'] && $sl['title'])->values();
  $duo = $categories->take(2);
@endphp

@section('content')
{{-- ============ Hero ============ --}}
@php
  $look = $featured->first(fn ($p) => $p->sizes && $p->in_stock) ?? $featured->first();
  $words = fn ($t) => explode(' ', trim($t));
@endphp
<section class="hero" data-hero aria-roledescription="carousel" aria-label="Featured collections">
  <div class="hero-word" aria-hidden="true">{{ $s['store_name'] }}</div>
  <div class="wrap hero-grid">
    <div class="hero-copy">
      <div class="hero-texts">
        @foreach($slides as $i => $sl)
          @php $w = $words($sl['title']); $last = array_pop($w); @endphp
          <div class="hero-text {{ $i === 0 ? 'is-active' : '' }}" data-slide="{{ $i }}" @if($i) aria-hidden="true" @endif>
            <span class="hero-tag"><b>New</b>{{ $sl['eyebrow'] }}</span>
            @if($i === 0)<h1 class="hero-title">@else<h2 class="hero-title">@endif
              {{ implode(' ', $w) }} <span class="stroke">{{ $last }}</span><span class="dot">.</span>
            @if($i === 0)</h1>@else</h2>@endif
            @if($sl['text'])<p class="hero-lead">{{ $sl['text'] }}</p>@endif
          </div>
        @endforeach
      </div>
      <div class="hero-actions">
        <a href="{{ route('shop') }}" class="btn btn-light">{{ $s['hero_cta'] ?: 'Shop now' }} <svg class="ico ico-sm"><use href="#i-arrow"/></svg></a>
        <a href="{{ route('shop', ['sale' => 1]) }}" class="btn btn-outline-light">Shop the sale</a>
      </div>
      <div class="hero-proof">
        <div class="avatars" aria-hidden="true">
          @foreach([1036623, 1043474, 1462637, 1183266] as $id)
            <img src="https://images.pexels.com/photos/{{ $id }}/pexels-photo-{{ $id }}.jpeg?auto=compress&cs=tinysrgb&w=96&h=96&fit=crop" alt="" loading="lazy">
          @endforeach
        </div>
        <div>
          <span style="display:flex;align-items:center;gap:8px">@include('partials.stars', ['rating' => 5]) <b>4.9 / 5</b></span>
          <small>Loved by 2,000+ happy customers</small>
        </div>
        @if($slides->count() > 1)
          <div class="hero-nav">
            <div class="hero-dots">
              @foreach($slides as $i => $sl)
                <button class="{{ $i === 0 ? 'is-active' : '' }}" data-go="{{ $i }}" aria-label="Slide {{ $i + 1 }}">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}<i></i></button>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>

    <div class="hero-art">
      <div class="art-ring" aria-hidden="true"></div>
      <div class="art-main">
        @foreach($slides as $i => $sl)
          <img class="{{ $i === 0 ? 'is-active' : '' }}" data-slide="{{ $i }}" src="{{ Shop::img($sl['img'], 1000) }}"
               srcset="{{ Shop::img($sl['img'], 600) }} 600w, {{ Shop::img($sl['img'], 1000) }} 1000w" sizes="(max-width:1024px) 76vw, 34vw"
               alt="" @if($i === 0) fetchpriority="high" @else loading="lazy" @endif>
        @endforeach
      </div>
      @if(! empty($s['hero_side_image']))
        <div class="art-second"><img src="{{ Shop::img($s['hero_side_image'], 500) }}" alt="" loading="lazy"></div>
      @endif
      @if($look)
        <a class="art-card" href="{{ route('product', $look) }}">
          <img src="{{ Shop::img($look->image, 160) }}" alt="">
          <span class="meta">
            <small>Shop the look</small>
            <b>{{ $look->name }}</b>
            <span class="p">{{ Shop::money($look->price) }}@if($look->discount_percent)<s>{{ Shop::money($look->compare_price) }}</s>@endif</span>
          </span>
          <span class="go"><svg class="ico ico-sm"><use href="#i-arrow"/></svg></span>
        </a>
      @endif
      <a class="art-badge" href="{{ route('shop', ['sale' => 1]) }}" aria-label="Shop the new season sale">
        <svg class="ring" viewBox="0 0 128 128" aria-hidden="true">
          <defs><path id="badge-circle" d="M64,64 m-48,0 a48,48 0 1,1 96,0 a48,48 0 1,1 -96,0"/></defs>
          <text><textPath href="#badge-circle">New season • Up to 30% off • </textPath></text>
        </svg>
        <svg class="ico"><use href="#i-arrow"/></svg>
      </a>
    </div>
  </div>

  <div class="hero-ticker" aria-hidden="true">
    <div class="ticker-track">
      @foreach(range(1, 2) as $_)
        @foreach($categories as $c)<a href="{{ route('category', $c) }}" tabindex="-1">{{ $c->name }}</a>@endforeach
      @endforeach
    </div>
  </div>
</section>

{{-- ============ Trust bar ============ --}}
<section class="trust">
  <div class="wrap">
    <div class="trust-item"><svg class="ico"><use href="#i-truck"/></svg><div><b>Free delivery</b><span>On orders over {{ Shop::money($s['free_shipping_over']) }}</span></div></div>
    <div class="trust-item"><svg class="ico"><use href="#i-cash"/></svg><div><b>Cash on delivery</b><span>Pay when it arrives</span></div></div>
    <div class="trust-item"><svg class="ico"><use href="#i-refresh"/></svg><div><b>Easy exchanges</b><span>Within 7 days</span></div></div>
    <div class="trust-item"><svg class="ico"><use href="#i-shield"/></svg><div><b>Quality checked</b><span>Every piece, by hand</span></div></div>
  </div>
</section>

{{-- ============ Categories ============ --}}
@if($categories->count())
<section class="section">
  <div class="wrap">
    <div class="section-head">
      <div><span class="eyebrow">Collections</span><h2 class="h2">Shop by category</h2></div>
      <a href="{{ route('shop') }}" class="link">View all</a>
    </div>
    <div class="cats">
      @foreach($categories as $i => $c)
        <a href="{{ route('category', $c) }}" class="cat reveal reveal-d{{ $i % 4 }}">
          <div class="cat-media"><img src="{{ Shop::img($c->image, 600) }}" alt="{{ $c->name }}" loading="lazy"></div>
          <div class="cat-info"><h3>{{ $c->name }}</h3><span>{{ $c->products_count }} items</span></div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ============ Featured / New tabs ============ --}}
<section class="section" style="padding-top:0">
  <div class="wrap" data-tabs>
    <div class="section-head center">
      <span class="eyebrow">Curated for you</span>
      <h2 class="h2" style="margin-bottom:22px">This week's edit</h2>
      <div class="tabs" role="tablist">
        <button class="is-active" data-tab="featured" role="tab" aria-selected="true">Featured</button>
        <button data-tab="new" role="tab" aria-selected="false">New arrivals</button>
        <button data-tab="best" role="tab" aria-selected="false">Bestsellers</button>
      </div>
    </div>
    <div class="tab-panel is-active" data-tab-panel="featured" role="tabpanel">
      <div class="grid">@foreach($featured as $p) @include('partials.product-card', ['p' => $p]) @endforeach</div>
    </div>
    <div class="tab-panel" data-tab-panel="new" role="tabpanel">
      <div class="grid">@foreach($newArrivals as $p) @include('partials.product-card', ['p' => $p]) @endforeach</div>
    </div>
    <div class="tab-panel" data-tab-panel="best" role="tabpanel">
      <div class="grid">@foreach($bestSellers as $p) @include('partials.product-card', ['p' => $p]) @endforeach</div>
    </div>
    <div style="text-align:center;margin-top:48px"><a href="{{ route('shop') }}" class="btn btn-ghost">View all products</a></div>
  </div>
</section>

{{-- ============ Editorial duo ============ --}}
@if($duo->count() === 2)
<section class="section" style="padding-top:0">
  <div class="wrap duo">
    @foreach($duo as $c)
      <a href="{{ route('category', $c) }}" class="duo-item reveal {{ $loop->index ? 'reveal-d1' : '' }}">
        <img src="{{ Shop::img($c->image, 1100) }}" alt="" loading="lazy">
        <div class="duo-copy">
          <span class="eyebrow">{{ $c->tagline ?: 'Collection' }}</span>
          <h3>{{ $c->name }}</h3>
          <span class="link">Shop {{ $c->name }}</span>
        </div>
      </a>
    @endforeach
  </div>
</section>
@endif

{{-- ============ Promo banner ============ --}}
<section class="promo">
  <img src="{{ Shop::img($s['promo_image'], 1920) }}" alt="" loading="lazy">
  <div class="wrap">
    <div class="promo-copy reveal">
      <span class="eyebrow">Limited time</span>
      <h2 class="h1" style="margin-top:14px">{{ $s['promo_title'] }}</h2>
      <p>{{ $s['promo_text'] }}</p>
      <a href="{{ route('shop', ['sale' => 1]) }}" class="btn btn-light">Shop the sale</a>
    </div>
  </div>
</section>

{{-- ============ Story ============ --}}
<section class="section">
  <div class="wrap story">
    <div class="story-img reveal"><img src="{{ Shop::img('https://images.pexels.com/photos/1488463/pexels-photo-1488463.jpeg', 1000) }}" alt="Inside our store" loading="lazy"></div>
    <div class="reveal reveal-d1">
      <span class="eyebrow">Our story</span>
      <blockquote>Fewer, better things — chosen with care and made to last.</blockquote>
      <p class="lead">{{ $s['about'] }}</p>
      <a href="{{ route('page', 'about') }}" class="link" style="margin-top:26px">Read our story</a>
    </div>
  </div>
</section>

{{-- ============ Reviews ============ --}}
@if($reviews->count())
<section class="section bg-2">
  <div class="wrap">
    <div class="section-head">
      <div><span class="eyebrow">Reviews</span><h2 class="h2">What our customers say</h2></div>
      <div class="muted small" style="display:flex;align-items:center;gap:8px">@include('partials.stars', ['rating' => 5]) <span>Rated 4.9 / 5 by verified buyers</span></div>
    </div>
    <div class="reviews">
      @foreach($reviews as $r)
        <figure class="review">
          @include('partials.stars', ['rating' => $r->rating])
          <p>“{{ $r->body }}”</p>
          <figcaption class="review-foot">
            @if($r->product)<img src="{{ Shop::img($r->product->image, 120) }}" alt="" loading="lazy">@endif
            <div><b>{{ $r->name }}</b><br><span class="muted">Purchased {{ $r->product?->name }}</span></div>
          </figcaption>
        </figure>
      @endforeach
    </div>
  </div>
</section>
@endif
@endsection
