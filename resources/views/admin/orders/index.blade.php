@extends('layouts.admin')
@php use App\Support\Shop; use App\Models\Order; $title = 'Orders'; @endphp
@section('actions')
  <a class="btn btn-light" href="{{ route('admin.orders.export', request()->query()) }}"><svg class="ico ico-sm"><use href="#a-download"/></svg><span class="hide-sm">Export CSV</span></a>
@endsection

@section('content')
<div class="panel">
  <div class="status-tabs">
    <a href="{{ route('admin.orders.index', request()->except('status', 'page')) }}" class="{{ ! request('status') ? 'is-active' : '' }}">All<span>{{ $counts->sum() }}</span></a>
    @foreach(Order::STATUSES as $k => $l)
      <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => $k])) }}" class="{{ request('status') === $k ? 'is-active' : '' }}">{{ $l }}<span>{{ $counts[$k] ?? 0 }}</span></a>
    @endforeach
  </div>
  <form class="filters">
    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <input class="input grow" name="q" value="{{ request('q') }}" placeholder="Order #, name, phone or email">
    <select class="select" name="payment" style="width:auto">
      <option value="">Any payment</option>
      @foreach(['unpaid' => 'Unpaid', 'paid' => 'Paid', 'refunded' => 'Refunded'] as $k => $l)<option value="{{ $k }}" @selected(request('payment') === $k)>{{ $l }}</option>@endforeach
    </select>
    <input class="input" type="date" name="from" value="{{ request('from') }}" aria-label="From date">
    <input class="input" type="date" name="to" value="{{ request('to') }}" aria-label="To date">
    <button class="btn">Filter</button>
    @if(request()->hasAny(['q', 'payment', 'from', 'to']))<a class="btn btn-light" href="{{ route('admin.orders.index', request()->only('status')) }}">Reset</a>@endif
  </form>

  <form action="{{ route('admin.orders.bulk') }}" method="post" id="bulk">
    @csrf
    <div class="filters" style="background:#FBFAF8">
      <span class="muted small">With selected:</span>
      <select class="select" name="status" style="width:auto">
        <option value="confirmed">Mark confirmed</option><option value="processing">Mark processing</option>
        <option value="shipped">Mark shipped</option><option value="delivered">Mark delivered</option>
      </select>
      <button class="btn btn-light btn-sm">Apply</button>
    </div>
  </form>

  <div class="table-wrap">
    <table class="t responsive">
      <thead><tr>
        <th style="width:34px"><input type="checkbox" data-check-all=".row-check" aria-label="Select all"></th>
        <th>Order</th><th>Date</th><th>Customer</th><th>Items</th><th>Payment</th><th>Status</th><th class="num">Total</th>
      </tr></thead>
      <tbody>
        @forelse($orders as $o)
          <tr>
            <td data-label="Select"><input type="checkbox" class="row-check" name="ids[]" value="{{ $o->id }}" form="bulk" aria-label="Select {{ $o->number }}"></td>
            <td data-label="Order"><a href="{{ route('admin.orders.show', $o) }}"><b>{{ $o->number }}</b></a></td>
            <td data-label="Date">{{ $o->created_at->format('M j, Y') }}<div class="muted small">{{ $o->created_at->format('g:i A') }}</div></td>
            <td data-label="Customer">{{ $o->name }}<div class="muted small">{{ $o->phone }} · {{ $o->city }}</div></td>
            <td data-label="Items">{{ $o->items_count }}</td>
            <td data-label="Payment"><span class="badge b-{{ $o->payment_status }}">{{ ucfirst($o->payment_status) }}</span><div class="muted small">{{ strtoupper($o->payment_method) }}</div></td>
            <td data-label="Status"><span class="badge b-{{ $o->status }}">{{ $o->status_label }}</span></td>
            <td data-label="Total" class="num"><b>{{ Shop::money($o->total) }}</b></td>
          </tr>
        @empty
          <tr><td colspan="8" class="empty full">No orders match these filters.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $orders->links('partials.pager') }}
</div>
@endsection
