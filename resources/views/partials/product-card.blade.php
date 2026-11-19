@php use App\Support\Shop; @endphp
<article class="card {{ $class ?? '' }}">
  <div class="card-media">
    <img src="{{ Shop::img($p->image, 600) }}" srcset="{{ Shop::img($p->image, 400) }} 400w, {{ Shop::img($p->image, 700) }} 700w" sizes="(max-width:760px) 50vw, 25vw" alt="{{ $p->name }}" loading="lazy">
    @if($p->hover_image)<img class="alt" src="{{ Shop::img($p->hover_image, 600) }}" alt="" loading="lazy">@endif
    <div class="badges">
      @if(! $p->in_stock)<span class="badge badge-out">Sold out</span>
      @elseif($p->discount_percent)<span class="badge badge-sale">−{{ $p->discount_percent }}%</span>@endif
      @if($p->badge && $p->badge !== 'Sale' && $p->in_stock)<span class="badge">{{ $p->badge }}</span>@endif
    </div>
    <button class="wish" data-wish="{{ $p->id }}" aria-label="Save {{ $p->name }} to wishlist" aria-pressed="false"><svg class="ico ico-sm"><use href="#i-heart"/></svg></button>
    @if($p->in_stock)
      <div class="quick">
        @if($p->sizes)
          <a class="btn btn-sm" href="{{ route('product', $p) }}">Choose size</a>
        @else
          <form action="{{ route('cart.add', $p) }}" method="post" data-ajax-cart>@csrf<button class="btn btn-sm">Add to bag</button></form>
        @endif
      </div>
    @endif
  </div>
  <div class="card-body">
    @if($p->relationLoaded('category') && $p->category)<div class="card-cat">{{ $p->category->name }}</div>@endif
    <h3 class="card-name"><a href="{{ route('product', $p) }}">{{ $p->name }}</a></h3>
    <div class="price">
      <span>{{ Shop::money($p->price) }}</span>
      @if($p->discount_percent)<s>{{ Shop::money($p->compare_price) }}</s>@endif
    </div>
  </div>
</article>
