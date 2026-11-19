@extends('pages._layout')
@php $title = 'Returns & exchanges'; @endphp
@section('page')
<p>Not quite right? We offer free exchanges within <b>7 days</b> of delivery.</p>
<h2>Conditions</h2>
<ul><li>Items must be unworn, unwashed and with original tags</li><li>Beauty products can only be returned if unopened</li><li>Sale items can be exchanged but not refunded</li></ul>
<h2>How to request an exchange</h2>
<p>Message us on WhatsApp or through the <a href="{{ route('contact') }}" style="text-decoration:underline">contact page</a> with your order number and the item you'd like to exchange. We'll arrange a pickup.</p>
@endsection
