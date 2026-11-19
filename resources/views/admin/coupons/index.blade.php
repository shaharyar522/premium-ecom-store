@extends('layouts.admin')
@php use App\Support\Shop; $title = 'Coupons'; @endphp
@section('actions')
  <a class="btn" href="{{ route('admin.coupons.create') }}"><svg class="ico ico-sm"><use href="#a-plus"/></svg><span class="hide-sm">New coupon</span></a>
@endsection

@section('content')
<div class="panel">
  <div class="table-wrap">
    <table class="t responsive">
      <thead><tr><th>Code</th><th>Discount</th><th>Minimum</th><th>Used</th><th>Expires</th><th>Status</th><th class="num">Actions</th></tr></thead>
      <tbody>
        @forelse($coupons as $c)
          @php $expired = $c->expires_at && $c->expires_at->endOfDay()->isPast(); @endphp
          <tr>
            <td data-label="Code"><b style="font-family:ui-monospace,monospace;letter-spacing:.04em">{{ $c->code }}</b></td>
            <td data-label="Discount">{{ $c->type === 'percent' ? $c->value.'% off' : Shop::money($c->value).' off' }}</td>
            <td data-label="Minimum">{{ $c->min_subtotal ? Shop::money($c->min_subtotal) : '—' }}</td>
            <td data-label="Used">{{ $c->used }}{{ $c->max_uses ? ' / '.$c->max_uses : '' }}</td>
            <td data-label="Expires">{{ $c->expires_at?->format('M j, Y') ?? 'Never' }}</td>
            <td data-label="Status">
              @if($expired)<span class="badge b-bad">Expired</span>@elseif($c->is_active)<span class="badge b-ok">Active</span>@else<span class="badge">Paused</span>@endif
            </td>
            <td data-label="Actions" class="num">
              <div style="display:inline-flex;gap:6px">
                <a class="btn btn-light btn-sm" href="{{ route('admin.coupons.edit', $c) }}">Edit</a>
                <form action="{{ route('admin.coupons.destroy', $c) }}" method="post" data-confirm="Delete coupon {{ $c->code }}?">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Delete</button></form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="empty full">No coupons yet. Create one to run a promotion.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
