@extends('layouts.admin')
@php use App\Support\Shop; $title = 'Reviews'; @endphp

@section('content')
<div class="panel">
  <div class="status-tabs">
    <a href="{{ route('admin.reviews.index') }}" class="{{ ! request('status') ? 'is-active' : '' }}">All</a>
    <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="{{ request('status') === 'pending' ? 'is-active' : '' }}">Waiting approval<span>{{ $pending }}</span></a>
    <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="{{ request('status') === 'approved' ? 'is-active' : '' }}">Published</a>
  </div>
  @forelse($reviews as $r)
    <div class="list-row" style="align-items:flex-start;flex-wrap:wrap">
      <img class="thumb" src="{{ Shop::img($r->product?->image, 120) }}" alt="">
      <div class="grow" style="min-width:220px">
        <div><b>{{ $r->name }}</b> <span style="color:#C99A2E">{{ str_repeat('★', $r->rating) }}</span><span style="color:#ddd">{{ str_repeat('★', 5 - $r->rating) }}</span> <span class="muted small">· {{ $r->created_at->diffForHumans() }}</span></div>
        <div class="muted small">on <a href="{{ $r->product ? route('product', $r->product) : '#' }}" target="_blank">{{ $r->product?->name }}</a></div>
        <p style="margin:8px 0 0">{{ $r->body }}</p>
      </div>
      <div style="display:flex;gap:6px;align-items:center">
        @if(! $r->is_approved)<span class="badge b-pending">Pending</span>@endif
        <form action="{{ route('admin.reviews.update', $r) }}" method="post">@csrf @method('PUT')
          <input type="hidden" name="is_approved" value="{{ $r->is_approved ? 0 : 1 }}">
          <button class="btn btn-sm {{ $r->is_approved ? 'btn-light' : '' }}">{{ $r->is_approved ? 'Hide' : 'Approve' }}</button>
        </form>
        <form action="{{ route('admin.reviews.destroy', $r) }}" method="post" data-confirm="Delete this review?">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" aria-label="Delete"><svg class="ico ico-sm"><use href="#a-trash"/></svg></button></form>
      </div>
    </div>
  @empty
    <div class="empty">No reviews here.</div>
  @endforelse
  {{ $reviews->links('partials.pager') }}
</div>
@endsection
