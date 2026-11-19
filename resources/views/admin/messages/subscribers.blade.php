@extends('layouts.admin')
@php $title = 'Subscribers'; @endphp
@section('actions')
  <a class="btn btn-light" href="{{ route('admin.subscribers.export') }}"><svg class="ico ico-sm"><use href="#a-download"/></svg><span class="hide-sm">Export CSV</span></a>
@endsection

@section('content')
<div class="panel" style="max-width:760px">
  <div class="panel-head"><h2>{{ $subscribers->total() }} newsletter subscribers</h2><span class="muted small">Import the CSV into Mailchimp, Brevo or similar.</span></div>
  @forelse($subscribers as $s)
    <div class="list-row"><div class="grow">{{ $s->email }}</div><span class="muted small">{{ $s->created_at->format('M j, Y') }}</span></div>
  @empty
    <div class="empty">No subscribers yet.</div>
  @endforelse
  {{ $subscribers->links('partials.pager') }}
</div>
@endsection
