@extends('layouts.admin')
@php use App\Support\Shop; $title = $latest->name; @endphp

@section('content')
<p style="margin:-6px 0 18px"><a class="muted" href="{{ route('admin.customers.index') }}">← All customers</a></p>
<div class="kpis" style="margin-bottom:20px">
  <div class="panel kpi"><div class="label">Total spent</div><div class="value">{{ Shop::money($spent) }}</div></div>
  <div class="panel kpi"><div class="label">Orders</div><div class="value">{{ $orders->count() }}</div></div>
  <div class="panel kpi"><div class="label">Average order</div><div class="value">{{ Shop::money($orders->count() ? $orders->avg('total') : 0) }}</div></div>
  <div class="panel kpi"><div class="label">Customer since</div><div class="value" style="font-size:20px">{{ $orders->last()->created_at->format('M j, Y') }}</div></div>
</div>
<div class="split">
  <div class="panel">
    <div class="panel-head"><h2>Orders</h2></div>
    <div class="table-wrap">
      <table class="t responsive">
        <thead><tr><th>Order</th><th>Date</th><th>Items</th><th>Status</th><th class="num">Total</th></tr></thead>
        <tbody>
          @foreach($orders as $o)
            <tr>
              <td data-label="Order"><a href="{{ route('admin.orders.show', $o) }}"><b>{{ $o->number }}</b></a></td>
              <td data-label="Date">{{ $o->created_at->format('M j, Y') }}</td>
              <td data-label="Items">{{ $o->items_count }}</td>
              <td data-label="Status"><span class="badge b-{{ $o->status }}">{{ $o->status_label }}</span></td>
              <td data-label="Total" class="num"><b>{{ Shop::money($o->total) }}</b></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="panel">
    <div class="panel-head"><h2>Contact</h2>@if($account)<span class="badge b-ok">Registered</span>@endif</div>
    <div class="panel-body">
      <dl class="dl">
        <dt>Name</dt><dd>{{ $latest->name }}</dd>
        <dt>Phone</dt><dd><a href="tel:{{ $phone }}">{{ $phone }}</a></dd>
        @if($latest->email)<dt>Email</dt><dd>{{ $latest->email }}</dd>@endif
        <dt>Address</dt><dd>{{ $latest->address }}, {{ $latest->city }}</dd>
        @if($account)<dt>Account</dt><dd>{{ $account->email }}</dd>@endif
      </dl>
    </div>
  </div>
</div>
@endsection
