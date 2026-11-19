@extends('layouts.admin')
@php $editing = $coupon->exists; $title = $editing ? 'Edit coupon' : 'New coupon'; @endphp

@section('content')
<p style="margin:-6px 0 18px"><a class="muted" href="{{ route('admin.coupons.index') }}">← All coupons</a></p>
<form class="panel" style="max-width:720px" action="{{ $editing ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" method="post">
  @csrf @if($editing) @method('PUT') @endif
  <div class="panel-body">
    <div class="field"><label for="code">Code</label><input id="code" name="code" value="{{ old('code', $coupon->code) }}" required placeholder="e.g. EID20" style="text-transform:uppercase;font-family:ui-monospace,monospace"></div>
    <div class="grid-2">
      <div class="field"><label for="type">Type</label>
        <select id="type" name="type"><option value="percent" @selected(old('type', $coupon->type) === 'percent')>Percentage (%)</option><option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Fixed amount</option></select></div>
      <div class="field"><label for="value">Value</label><input id="value" type="number" min="1" name="value" value="{{ old('value', $coupon->value) }}" required></div>
    </div>
    <div class="grid-3">
      <div class="field"><label for="min">Minimum order</label><input id="min" type="number" min="0" name="min_subtotal" value="{{ old('min_subtotal', $coupon->min_subtotal) }}"></div>
      <div class="field"><label for="max">Usage limit</label><input id="max" type="number" min="1" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" placeholder="Unlimited"></div>
      <div class="field"><label for="exp">Expires on</label><input id="exp" type="date" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d')) }}"></div>
    </div>
    <label class="switch" style="margin-bottom:20px"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active))> Active</label><br>
    <button class="btn">{{ $editing ? 'Save coupon' : 'Create coupon' }}</button>
  </div>
</form>
@endsection
