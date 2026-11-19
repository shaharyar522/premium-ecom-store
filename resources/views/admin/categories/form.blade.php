@extends('layouts.admin')
@php use App\Support\Shop; $editing = $category->exists; $title = $editing ? 'Edit category' : 'New category'; @endphp

@section('content')
<p style="margin:-6px 0 18px"><a class="muted" href="{{ route('admin.categories.index') }}">← All categories</a></p>
<form class="panel" style="max-width:760px" action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="post" enctype="multipart/form-data">
  @csrf @if($editing) @method('PUT') @endif
  <div class="panel-body">
    <div class="grid-2">
      <div class="field"><label for="name">Name</label><input id="name" name="name" value="{{ old('name', $category->name) }}" required></div>
      <div class="field"><label for="slug">URL slug</label><input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="auto from name"></div>
    </div>
    <div class="field"><label for="tagline">Tagline</label><input id="tagline" name="tagline" value="{{ old('tagline', $category->tagline) }}" placeholder="Shown on the collection page"></div>
    <div class="grid-2">
      <div class="field"><label for="sort">Display order</label><input id="sort" type="number" min="0" name="sort" value="{{ old('sort', $category->sort ?? 0) }}"><span class="help">Lower numbers show first.</span></div>
      <div class="field" style="justify-content:flex-end"><label class="switch"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))> Visible in store</label></div>
    </div>
    <div class="field">
      <label>Image</label>
      <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap">
        @if($category->image)<img class="thumb" style="width:72px;height:90px" src="{{ Shop::img($category->image, 200) }}" alt="">@endif
        <input type="file" name="image_file" accept="image/*">
      </div>
      <input name="image_url" placeholder="…or paste an image link" value="{{ old('image_url') }}" style="margin-top:8px">
    </div>
    <button class="btn">{{ $editing ? 'Save category' : 'Create category' }}</button>
  </div>
</form>
@endsection
