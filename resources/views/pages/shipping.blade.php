@extends('pages._layout')
@php $title = 'Delivery information'; $s = \App\Support\Shop::settings(); @endphp
@section('page')
<p>We deliver nationwide through trusted courier partners.</p>
<h2>Charges</h2>
<p>Standard delivery is {{ \App\Support\Shop::money($s['shipping_fee']) }}. Orders over {{ \App\Support\Shop::money($s['free_shipping_over']) }} ship free.</p>
<h2>Timelines</h2>
<ul><li>Major cities: 2–4 working days</li><li>Other areas: 4–6 working days</li></ul>
<p>Once your order ships you'll receive the courier tracking number, and you can follow it anytime on the <a href="{{ route('track') }}" style="text-decoration:underline">Track order</a> page.</p>
@endsection
