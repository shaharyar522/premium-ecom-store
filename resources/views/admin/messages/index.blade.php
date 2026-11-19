@extends('layouts.admin')
@php $title = 'Messages'; @endphp

@section('content')
<div class="panel">
  @forelse($messages as $m)
    <a class="list-row" href="{{ route('admin.messages.show', $m) }}" style="{{ $m->is_read ? '' : 'background:#FBF7F1' }}">
      <span class="avatar" style="background:{{ $m->is_read ? '#BDB6AA' : 'var(--accent)' }}">{{ strtoupper(mb_substr($m->name, 0, 1)) }}</span>
      <div class="grow">
        <div style="display:flex;justify-content:space-between;gap:10px"><b>{{ $m->name }}</b><span class="muted small" style="white-space:nowrap">{{ $m->created_at->diffForHumans() }}</span></div>
        <div class="ellipsis {{ $m->is_read ? 'muted' : '' }}" style="{{ $m->is_read ? '' : 'font-weight:600' }}">{{ $m->body }}</div>
      </div>
    </a>
  @empty
    <div class="empty">No messages yet. Contact-form messages from the store appear here.</div>
  @endforelse
  {{ $messages->links('partials.pager') }}
</div>
@endsection
