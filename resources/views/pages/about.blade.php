@extends('pages._layout')
@php $title = 'Our story'; $eyebrow = 'About us'; $s = \App\Support\Shop::settings(); @endphp
@section('page')
<p class="lead" style="max-width:none">{{ $s['about'] }}</p>
<img src="{{ \App\Support\Shop::img('https://images.pexels.com/photos/325876/pexels-photo-325876.jpeg', 1400) }}" alt="" style="border-radius:var(--radius);margin:32px 0;aspect-ratio:16/9;object-fit:cover;width:100%">
<h2>What we believe</h2>
<p>Good things should be made well, sold honestly and delivered with care. We work with a small group of trusted makers, check every piece by hand and price it fairly — no inflated "sales", no shortcuts.</p>
<h2>Our promise</h2>
<ul><li>Quality checked before every dispatch</li><li>Cash on delivery across the country</li><li>Free exchanges within 7 days</li><li>Real people on WhatsApp when you need help</li></ul>
@endsection
