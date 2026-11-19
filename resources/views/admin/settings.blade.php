@extends('layouts.admin')
@php use App\Support\Shop; $title = 'Settings'; @endphp

@section('content')
<p class="muted" style="margin:-6px 0 18px;max-width:760px">Everything a new client needs — name, logo, colours, homepage text, contact details and checkout options. Changes go live instantly.</p>
<form action="{{ route('admin.settings.update') }}" method="post" enctype="multipart/form-data" style="max-width:900px">
  @csrf @method('PUT')
  <div class="stack">
    @foreach($groups as $group => $fields)
      <div class="panel">
        <div class="panel-head"><h2>{{ $group }}</h2></div>
        <div class="panel-body grid-2">
          @foreach($fields as $key => $f)
            @php [$label, $type] = $f; $help = $f[2] ?? null; $val = old($key, $s[$key] ?? ''); $wide = in_array($type, ['textarea', 'image'], true); @endphp
            <div class="field" style="{{ $wide ? 'grid-column:1/-1' : '' }}">
              @if($type === 'toggle')
                <label class="switch" style="margin-top:22px"><input type="checkbox" name="{{ $key }}" value="1" @checked($val)> {{ $label }}</label>
              @else
                <label for="f-{{ $key }}">{{ $label }}</label>
                @switch($type)
                  @case('textarea')<textarea id="f-{{ $key }}" name="{{ $key }}" rows="3">{{ $val }}</textarea>@break
                  @case('number')<input id="f-{{ $key }}" type="number" min="0" name="{{ $key }}" value="{{ $val }}">@break
                  @case('color')
                    <div style="display:flex;gap:10px;align-items:center"><input id="f-{{ $key }}" type="color" name="{{ $key }}" value="{{ $val }}"><code class="muted">{{ $val }}</code></div>
                    @break
                  @case('image')
                    <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap">
                      @if($val)<img src="{{ Shop::img($val, 300) }}" alt="" style="height:70px;width:auto;max-width:160px;object-fit:cover;border-radius:8px;border:1px solid var(--line);background:#F5F4F1">@endif
                      <input id="f-{{ $key }}" type="file" name="{{ $key }}" accept="image/*">
                      @if(\App\Models\Setting::whereKey($key)->exists())<label class="switch small"><input type="checkbox" name="remove_{{ $key }}" value="1"> Reset to default</label>@endif
                    </div>
                    @break
                  @default<input id="f-{{ $key }}" name="{{ $key }}" value="{{ $val }}">
                @endswitch
              @endif
              @if($help)<span class="help">{{ $help }}</span>@endif
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
  <div class="sticky-save"><button class="btn" style="min-height:46px;padding:0 28px">Save settings</button></div>
</form>
@endsection
