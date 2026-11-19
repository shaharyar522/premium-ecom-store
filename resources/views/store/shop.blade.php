@extends('layouts.store')
@php
  $title = $category?->name ?? (request('q') ? 'Search: '.request('q') : (request('sale') ? 'Sale' : 'Shop all'));
  $base = $category ? route('category', $category) : route('shop');
  $keep = fn (array $over) => $base.'?'.http_build_query(array_filter(array_merge(request()->except('page'), $over), fn ($v) => $v !== null && $v !== ''));
@endphp

@section('content')
<section class="page-head">
  <div class="wrap">
    <nav class="crumbs" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>{{ $category ? 'Collections' : 'Shop' }}</span>@if($category)<span>{{ $category->name }}</span>@endif</nav>
    <h1 class="h1">{{ $title }}</h1>
    @if($category?->tagline)<p class="lead" style="margin-top:10px">{{ $category->tagline }}</p>@endif
  </div>
</section>

<section class="wrap" style="padding-bottom:40px">
  <div class="shop">
    <aside class="filters" data-panel="filters" aria-label="Filters">
      <div style="display:flex;justify-content:space-between;align-items:center" class="filter-mobile-head">
        <h3 class="h3">Filters</h3>
        <button class="icon-btn filter-btn" data-close aria-label="Close filters"><svg class="ico"><use href="#i-close"/></svg></button>
      </div>
      <div class="filter-group">
        <h4>Category</h4>
        <div class="filter-list">
          <a href="{{ route('shop', request()->only('q', 'sort')) }}" class="{{ ! $category ? 'is-active' : '' }}">All products</a>
          @foreach($categories as $c)
            <a href="{{ route('category', $c) }}" class="{{ $category?->id === $c->id ? 'is-active' : '' }}">{{ $c->name }}</a>
          @endforeach
        </div>
      </div>
      @if($sizes->count())
      <div class="filter-group">
        <h4>Size</h4>
        <div class="size-grid">
          @foreach($sizes as $sz)
            <a href="{{ $keep(['size' => request('size') === $sz ? null : $sz]) }}" class="{{ request('size') === $sz ? 'is-active' : '' }}">{{ $sz }}</a>
          @endforeach
        </div>
      </div>
      @endif
      <form class="filter-group" action="{{ $base }}">
        @foreach(request()->only('q', 'sort', 'size') as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
        <h4>Price</h4>
        <div class="price-row">
          <input class="input" type="number" name="min" min="0" placeholder="Min" value="{{ request('min') }}" aria-label="Minimum price">
          <span class="muted">–</span>
          <input class="input" type="number" name="max" min="0" placeholder="{{ $priceMax }}" value="{{ request('max') }}" aria-label="Maximum price">
        </div>
        <label class="check" style="margin-top:14px"><input type="checkbox" name="sale" value="1" @checked(request('sale'))> On sale</label><br>
        <label class="check" style="margin-top:8px"><input type="checkbox" name="instock" value="1" @checked(request('instock'))> In stock only</label>
        <button class="btn btn-sm btn-block" style="margin-top:16px">Apply</button>
      </form>
      @if(request()->hasAny(['min', 'max', 'sale', 'instock', 'size', 'q']))
        <a href="{{ $base }}" class="link" style="margin-top:18px">Clear all filters</a>
      @endif
    </aside>

    <div>
      <div class="toolbar">
        <div style="display:flex;gap:10px;align-items:center">
          <button class="btn btn-ghost btn-sm filter-btn" data-open="filters"><svg class="ico ico-sm"><use href="#i-filter"/></svg> Filters</button>
          <span class="muted small">{{ $products->total() }} {{ \Illuminate\Support\Str::plural('product', $products->total()) }}</span>
        </div>
        <form action="{{ $base }}">
          @foreach(request()->except('sort', 'page') as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
          <label class="sr-only" for="sort">Sort by</label>
          <select id="sort" class="select" name="sort" data-autosubmit>
            <option value="">Newest</option>
            <option value="popular" @selected(request('sort') === 'popular')>Most popular</option>
            <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: low to high</option>
            <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: high to low</option>
          </select>
        </form>
      </div>

      @if($products->count())
        <div class="grid grid-3">@foreach($products as $p) @include('partials.product-card', ['p' => $p]) @endforeach</div>
        {{ $products->links('partials.pager') }}
      @else
        <div class="empty card-box">
          <svg class="ico"><use href="#i-search"/></svg>
          <h3 class="h3">Nothing found</h3>
          <p class="muted">Try another search or clear the filters.</p>
          <a href="{{ route('shop') }}" class="btn" style="margin-top:8px">See all products</a>
        </div>
      @endif
    </div>
  </div>
</section>
@endsection
