@extends('layouts.admin')
@php $title = 'Staff'; @endphp

@section('content')
<div class="split">
  <div class="panel">
    <div class="panel-head"><h2>Dashboard access</h2></div>
    @foreach($staff as $u)
      <div class="list-row">
        <span class="avatar">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
        <div class="grow"><b>{{ $u->name }}</b> @if($u->id === auth()->id())<span class="muted small">(you)</span>@endif<div class="muted small">{{ $u->email }}</div></div>
        <span class="badge {{ $u->role === 'admin' ? 'b-shipped' : '' }}">{{ $u->role === 'admin' ? 'Owner' : 'Staff' }}</span>
        @if($u->id !== auth()->id())
          <form action="{{ route('admin.staff.destroy', $u) }}" method="post" data-confirm="Remove dashboard access for {{ $u->name }}?">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Remove</button></form>
        @endif
      </div>
    @endforeach
  </div>
  <form class="panel" action="{{ route('admin.staff.store') }}" method="post">
    @csrf
    <div class="panel-head"><h2>Add a team member</h2></div>
    <div class="panel-body">
      <div class="field"><label for="name">Name</label><input id="name" name="name" value="{{ old('name') }}" required></div>
      <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required></div>
      <div class="field"><label for="password">Password</label><input id="password" type="password" name="password" required autocomplete="new-password"><span class="help">At least 8 characters. To change your own password, add yourself again with a new one.</span></div>
      <div class="field"><label for="role">Role</label>
        <select id="role" name="role"><option value="staff">Staff — orders, products, customers</option><option value="admin">Owner — everything incl. settings & staff</option></select></div>
      <button class="btn">Give access</button>
    </div>
  </form>
</div>
@endsection
