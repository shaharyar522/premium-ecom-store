<span class="stars" aria-label="{{ $rating }} out of 5 stars">@for($i = 1; $i <= 5; $i++)<svg class="ico {{ $i <= round($rating) ? '' : 'off' }}"><use href="#i-star"/></svg>@endfor</span>
