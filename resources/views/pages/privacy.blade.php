@extends('pages._layout')
@php $title = 'Privacy policy'; $s = \App\Support\Shop::settings(); @endphp
@section('page')
<p>{{ $s['store_name'] }} respects your privacy. This page explains what we collect and why.</p>
<h2>What we collect</h2>
<p>Your name, phone, email and delivery address when you place an order or create an account — only what we need to deliver your order and support you.</p>
<h2>How we use it</h2>
<p>To process and deliver orders, contact you about them, and — only if you subscribe — send occasional news. We never sell your data.</p>
<h2>Contact</h2>
<p>Questions? Email us at {{ $s['email'] }}.</p>
@endsection
