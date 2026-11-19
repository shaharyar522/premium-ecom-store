@extends('pages._layout')
@php $title = 'Frequently asked questions'; $eyebrow = 'Help'; $s = \App\Support\Shop::settings(); @endphp
@section('page')
<div class="accordion">
  <details open><summary>How long does delivery take?</summary><div class="acc-body">Orders are dispatched within 24 hours and usually arrive in 2–4 working days in major cities, and 4–6 days elsewhere.</div></details>
  <details><summary>How much is delivery?</summary><div class="acc-body">Delivery is {{ \App\Support\Shop::money($s['shipping_fee']) }}, and free on orders over {{ \App\Support\Shop::money($s['free_shipping_over']) }}.</div></details>
  <details><summary>Can I pay cash on delivery?</summary><div class="acc-body">Yes — pay in cash when your parcel arrives. Bank transfer is also available at checkout.</div></details>
  <details><summary>How do I exchange an item?</summary><div class="acc-body">Message us on WhatsApp within 7 days of delivery with your order number. Items must be unworn with tags attached.</div></details>
  <details><summary>How do I track my order?</summary><div class="acc-body">Use the <a href="{{ route('track') }}" style="text-decoration:underline">Track order</a> page with your order number and phone number.</div></details>
  <details><summary>Are your products authentic?</summary><div class="acc-body">Every item is sourced from trusted makers and checked by hand before dispatch.</div></details>
</div>
@endsection
