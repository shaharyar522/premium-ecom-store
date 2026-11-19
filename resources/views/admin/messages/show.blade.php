@extends('layouts.admin')
@php use App\Support\Shop; $title = 'Message from '.$message->name; @endphp

@section('content')
<p style="margin:-6px 0 18px"><a class="muted" href="{{ route('admin.messages.index') }}">← All messages</a></p>
<div class="panel" style="max-width:760px">
  <div class="panel-head"><h2>{{ $message->name }}</h2><span class="muted small">{{ $message->created_at->format('M j, Y · g:i A') }}</span></div>
  <div class="panel-body">
    <dl class="dl" style="margin-bottom:18px">
      @if($message->email)<dt>Email</dt><dd><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd>@endif
      @if($message->phone)<dt>Phone</dt><dd><a href="tel:{{ $message->phone }}">{{ $message->phone }}</a></dd>@endif
    </dl>
    <p style="white-space:pre-line;font-size:15px;margin:0 0 22px">{{ $message->body }}</p>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      @if($message->email)<a class="btn" href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: your message to '.Shop::get('store_name')) }}">Reply by email</a>@endif
      @if($message->phone)
        @php $wa = preg_replace('/\D/', '', $message->phone); if (str_starts_with($wa, '0')) $wa = '92'.substr($wa, 1); @endphp
        <a class="btn" style="background:#1FAF5A;border-color:#1FAF5A" target="_blank" rel="noopener" href="https://wa.me/{{ $wa }}">Reply on WhatsApp</a>
      @endif
      <form action="{{ route('admin.messages.destroy', $message) }}" method="post" data-confirm="Delete this message?">@csrf @method('DELETE')<button class="btn btn-danger">Delete</button></form>
    </div>
  </div>
</div>
@endsection
