@extends('pages._layout')
@php $title = 'Terms of service'; $s = \App\Support\Shop::settings(); @endphp
@section('page')
<p>By placing an order with {{ $s['store_name'] }} you agree to these terms.</p>
<h2>Orders & pricing</h2>
<p>All prices are in local currency and include applicable taxes. We may cancel an order if an item is out of stock or a price was shown in error — you'll be informed right away.</p>
<h2>Delivery</h2>
<p>Delivery times are estimates. Please make sure your phone number and address are correct.</p>
<h2>Exchanges</h2>
<p>See our <a href="{{ route('page', 'returns') }}" style="text-decoration:underline">returns & exchanges</a> policy.</p>
@endsection
