@extends('layouts.admin')
@php use App\Support\Shop; use App\Models\Order; $title = 'Dashboard'; @endphp

@section('content')
<div class="stack">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
    <div>
      <div style="font-size:22px;font-weight:800;letter-spacing:-.02em">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ explode(' ', auth()->user()->name)[0] }} 👋</div>
      <div class="muted">Today's sales so far: <b style="color:var(--ink)">{{ Shop::money($today) }}</b></div>
    </div>
    <div class="seg" role="group" aria-label="Period">
      @foreach([7 => '7 days', 30 => '30 days', 90 => '90 days'] as $d => $l)
        <a href="{{ route('admin.dashboard', ['days' => $d]) }}" class="{{ $days === $d ? 'is-active' : '' }}">{{ $l }}</a>
      @endforeach
    </div>
  </div>

  @if(array_sum($pending))
  <div class="alerts">
    @if($pending['orders'])<a class="alert-chip" href="{{ route('admin.orders.index', ['status' => 'pending']) }}"><svg class="ico ico-sm"><use href="#a-cart"/></svg><b>{{ $pending['orders'] }}</b> new orders to confirm</a>@endif
    @if($pending['reviews'])<a class="alert-chip" href="{{ route('admin.reviews.index', ['status' => 'pending']) }}"><svg class="ico ico-sm"><use href="#a-star"/></svg><b>{{ $pending['reviews'] }}</b> reviews waiting</a>@endif
    @if($pending['messages'])<a class="alert-chip" href="{{ route('admin.messages.index') }}"><svg class="ico ico-sm"><use href="#a-mail"/></svg><b>{{ $pending['messages'] }}</b> unread messages</a>@endif
    @if($lowStock->count())<a class="alert-chip" href="{{ route('admin.products.index', ['stock' => 'low']) }}"><svg class="ico ico-sm"><use href="#a-alert"/></svg><b>{{ $lowStock->count() }}</b> products low on stock</a>@endif
  </div>
  @endif

  <div class="kpis">
    @foreach($kpi as $k)
      <div class="panel kpi">
        <div class="label">{{ $k['label'] }} · {{ $days }}d</div>
        <div class="value">{{ $k['value'] }}</div>
        @if(! is_null($k['change']))
          <span class="delta {{ $k['change'] >= 0 ? 'up' : 'down' }}">{{ $k['change'] >= 0 ? '▲' : '▼' }} {{ abs($k['change']) }}%</span> <span class="muted small">vs previous {{ $days }}d</span>
        @else
          <span class="muted small">&nbsp;</span>
        @endif
      </div>
    @endforeach
  </div>

  <div class="split-2">
    <div class="panel">
      <div class="panel-head"><h2>Revenue</h2><span class="muted small">Excludes cancelled & returned orders</span></div>
      <div class="panel-body"><div class="chart-box"><canvas id="sales" aria-label="Revenue per day chart" role="img"></canvas></div></div>
    </div>
    <div class="panel">
      <div class="panel-head"><h2>Orders by status</h2><a class="small muted" href="{{ route('admin.orders.index') }}">All orders →</a></div>
      <div class="panel-body">
        @php $totalOrders = max(1, $statusCounts->sum()); @endphp
        @foreach(Order::STATUSES as $key => $label)
          <a href="{{ route('admin.orders.index', ['status' => $key]) }}" style="display:block;margin-bottom:14px">
            <div style="display:flex;justify-content:space-between;margin-bottom:6px"><span class="badge b-{{ $key }}">{{ $label }}</span><b>{{ $statusCounts[$key] ?? 0 }}</b></div>
            <div class="bar"><i style="width:{{ round(($statusCounts[$key] ?? 0) * 100 / $totalOrders) }}%"></i></div>
          </a>
        @endforeach
      </div>
    </div>
  </div>

  <div class="split-2">
    <div class="panel">
      <div class="panel-head"><h2>Recent orders</h2><a class="btn btn-light btn-sm" href="{{ route('admin.orders.index') }}">View all</a></div>
      <div class="table-wrap">
        <table class="t responsive">
          <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th class="num">Total</th></tr></thead>
          <tbody>
            @forelse($recent as $o)
              <tr onclick="location='{{ route('admin.orders.show', $o) }}'" style="cursor:pointer">
                <td data-label="Order"><a href="{{ route('admin.orders.show', $o) }}"><b>{{ $o->number }}</b></a><div class="muted small">{{ $o->created_at->diffForHumans() }}</div></td>
                <td data-label="Customer">{{ $o->name }}<div class="muted small">{{ $o->city }}</div></td>
                <td data-label="Status"><span class="badge b-{{ $o->status }}">{{ $o->status_label }}</span></td>
                <td data-label="Total" class="num"><b>{{ Shop::money($o->total) }}</b></td>
              </tr>
            @empty
              <tr><td colspan="4" class="empty full">No orders yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="stack">
      <div class="panel">
        <div class="panel-head"><h2>Top products · {{ $days }}d</h2></div>
        @forelse($top as $t)
          <div class="list-row">
            <img class="thumb" src="{{ Shop::img($t->image, 120) }}" alt="">
            <div class="grow"><div class="ellipsis" style="font-weight:700">{{ $t->name }}</div><div class="muted small">{{ $t->units }} sold · {{ Shop::money($t->sales) }}</div></div>
          </div>
        @empty
          <div class="empty">No sales in this period.</div>
        @endforelse
      </div>
      <div class="panel">
        <div class="panel-head"><h2>Low stock</h2><a class="small muted" href="{{ route('admin.products.index', ['stock' => 'low']) }}">Manage →</a></div>
        @forelse($lowStock as $p)
          <a class="list-row" href="{{ route('admin.products.edit', $p) }}">
            <img class="thumb" src="{{ Shop::img($p->image, 120) }}" alt="">
            <div class="grow ellipsis" style="font-weight:600">{{ $p->name }}</div>
            <span class="badge {{ $p->stock ? 'b-pending' : 'b-bad' }}">{{ $p->stock ? $p->stock.' left' : 'Sold out' }}</span>
          </a>
        @empty
          <div class="empty">All products are well stocked. 👌</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  (() => {
    const data = @json($series);
    const el = document.getElementById('sales');
    if (!window.Chart || !el) return;
    const ctx = el.getContext('2d');
    const accent = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#9A6B47';
    const money = (v) => @json(Shop::get('currency', 'Rs')) + ' ' + Number(v).toLocaleString();
    new Chart(ctx, {
      type: 'bar',
      data: { labels: data.map((d) => d.label), datasets: [{ data: data.map((d) => d.value), backgroundColor: accent, hoverBackgroundColor: '#17140F', borderRadius: 4, maxBarThickness: 22 }] },
      options: {
        maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
        plugins: { legend: { display: false }, tooltip: { backgroundColor: '#17140F', padding: 10, displayColors: false, callbacks: { label: (c) => money(c.parsed.y) } } },
        scales: {
          x: { grid: { display: false }, ticks: { maxTicksLimit: 7, color: '#6E6A63', font: { size: 11 } }, border: { display: false } },
          y: { grid: { color: '#EFEDE8' }, border: { display: false }, ticks: { maxTicksLimit: 5, color: '#6E6A63', font: { size: 11 }, callback: (v) => v >= 1000 ? (v / 1000) + 'k' : v } },
        },
      },
    });
  })();
</script>
@endpush
