@extends('layouts.admin')
@php use App\Support\Shop; $title = 'Customers'; @endphp

@section('content')
<div class="panel">
  <form class="filters">
    <input class="input grow" name="q" value="{{ request('q') }}" placeholder="Search name, phone or email">
    <select class="select" name="sort" style="width:auto" onchange="this.form.submit()">
      <option value="">Top spenders</option><option value="recent" @selected(request('sort') === 'recent')>Most recent</option>
    </select>
    <button class="btn">Search</button>
    <span class="muted small" style="margin-left:auto">{{ $customers->total() }} buyers · {{ $accounts }} registered accounts</span>
  </form>
  <div class="table-wrap">
    <table class="t responsive">
      <thead><tr><th>Customer</th><th>City</th><th>Orders</th><th>Last order</th><th class="num">Total spent</th></tr></thead>
      <tbody>
        @forelse($customers as $c)
          <tr onclick="location='{{ route('admin.customers.show', $c->phone) }}'" style="cursor:pointer">
            <td class="full"><a href="{{ route('admin.customers.show', $c->phone) }}" style="display:flex;gap:12px;align-items:center">
              <span class="avatar" style="background:var(--accent)">{{ strtoupper(mb_substr($c->name, 0, 1)) }}</span>
              <span><b>{{ $c->name }}</b>@if($c->user_id) <span class="badge b-ok" style="margin-left:4px">Account</span>@endif<div class="muted small">{{ $c->phone }}{{ $c->email ? ' · '.$c->email : '' }}</div></span>
            </a></td>
            <td data-label="City">{{ $c->city }}</td>
            <td data-label="Orders">{{ $c->orders }}</td>
            <td data-label="Last order">{{ \Illuminate\Support\Carbon::parse($c->last_order)->diffForHumans() }}</td>
            <td data-label="Spent" class="num"><b>{{ Shop::money($c->spent) }}</b></td>
          </tr>
        @empty
          <tr><td colspan="5" class="empty full">No customers yet — they appear here after their first order.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $customers->links('partials.pager') }}
</div>
@endsection
