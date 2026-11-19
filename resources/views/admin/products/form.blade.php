@extends('layouts.admin')
@php use App\Support\Shop; $editing = $product->exists; $title = $editing ? 'Edit product' : 'New product'; @endphp
@section('actions')
  @if($editing)
    <a class="btn btn-light" href="{{ route('product', $product) }}" target="_blank"><svg class="ico ico-sm"><use href="#a-external"/></svg><span class="hide-sm">View</span></a>
  @endif
@endsection

@section('content')
<p style="margin:-6px 0 18px"><a class="muted" href="{{ route('admin.products.index') }}">← All products</a></p>
<form action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}" method="post" enctype="multipart/form-data">
  @csrf @if($editing) @method('PUT') @endif
  <div class="split">
    <div class="stack">
      <div class="panel">
        <div class="panel-head"><h2>Basic information</h2></div>
        <div class="panel-body">
          <div class="field"><label for="name">Product name</label><input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="160"></div>
          <div class="field"><label for="short">Short description</label><input id="short" name="short_description" value="{{ old('short_description', $product->short_description) }}" maxlength="300"><span class="help">One line shown under the price.</span></div>
          <div class="field"><label for="desc">Full description</label><textarea id="desc" name="description" rows="7">{{ old('description', $product->description) }}</textarea><span class="help">Line breaks are kept. Use "•" for bullet points.</span></div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head"><h2>Images</h2><span class="muted small">First image is the cover. Recommended 1200×1500 (4:5).</span></div>
        <div class="panel-body">
          @if($editing && $product->images->count())
            <div class="img-grid" style="margin-bottom:16px">
              @foreach($product->images as $i => $img)
                <div class="img-tile">
                  <img src="{{ Shop::img($img->path, 300) }}" alt="">
                  <div class="tools">
                    <label title="Make cover"><input type="radio" name="cover" value="{{ $img->id }}" @checked($i === 0) style="margin:0"> Cover</label>
                    <button type="submit" form="del-img-{{ $img->id }}" title="Remove"><svg class="ico ico-sm"><use href="#a-trash"/></svg></button>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
          <label class="drop">
            <input type="file" name="images[]" accept="image/*" multiple hidden data-preview="#new-previews">
            <b style="color:var(--ink)">Click to upload photos</b><br><span class="small">JPG, PNG or WebP · up to 5 MB each · select several at once</span>
          </label>
          <div id="new-previews" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px"></div>
          <div class="field" style="margin:16px 0 0">
            <label for="urls">…or paste image links (one per line)</label>
            <textarea id="urls" name="image_urls" rows="2" placeholder="https://images.pexels.com/photos/…">{{ old('image_urls') }}</textarea>
            <span class="help">Free stock photos from Pexels or Unsplash work great while setting up a store.</span>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head"><h2>Pricing & inventory</h2></div>
        <div class="panel-body">
          <div class="grid-3">
            <div class="field"><label for="price">Price ({{ Shop::get('currency') }})</label><input id="price" type="number" min="0" name="price" value="{{ old('price', $product->price) }}" required></div>
            <div class="field"><label for="compare">Compare-at price</label><input id="compare" type="number" min="0" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}"><span class="help">Higher "was" price → shows a sale badge.</span></div>
            <div class="field"><label for="stock">Stock quantity</label><input id="stock" type="number" min="0" name="stock" value="{{ old('stock', $product->stock) }}" required></div>
          </div>
          <div class="grid-2">
            <div class="field"><label for="sku">SKU / code</label><input id="sku" name="sku" value="{{ old('sku', $product->sku) }}"></div>
            <div class="field"><label for="slug">URL slug</label><input id="slug" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="auto from name"></div>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head"><h2>Options</h2></div>
        <div class="panel-body grid-2">
          <div class="field"><label for="sizes">Sizes</label><input id="sizes" name="sizes" value="{{ old('sizes', implode(', ', $product->sizes ?? [])) }}" placeholder="S, M, L, XL"><span class="help">Comma separated. Leave empty if the product has no sizes.</span></div>
          <div class="field"><label for="colors">Colours</label><input id="colors" name="colors" value="{{ old('colors', implode(', ', $product->colors ?? [])) }}" placeholder="Black, Ivory"><span class="help">Comma separated.</span></div>
        </div>
      </div>
    </div>

    <div class="stack" style="position:sticky;top:84px">
      <div class="panel">
        <div class="panel-head"><h2>Visibility</h2></div>
        <div class="panel-body" style="display:grid;gap:14px">
          <label class="switch"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Live in store</label>
          <label class="switch"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Featured on homepage</label>
        </div>
      </div>
      <div class="panel">
        <div class="panel-head"><h2>Organisation</h2></div>
        <div class="panel-body">
          <div class="field"><label for="cat">Category</label>
            <select id="cat" name="category_id"><option value="">— None —</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
          <div class="field" style="margin:0"><label for="badge">Badge</label><input id="badge" name="badge" list="badges" value="{{ old('badge', $product->badge) }}" placeholder="e.g. New, Bestseller"><datalist id="badges"><option>New</option><option>Bestseller</option><option>Limited</option><option>Gift</option></datalist></div>
        </div>
      </div>
      <button class="btn" style="min-height:46px">{{ $editing ? 'Save product' : 'Create product' }}</button>
      @if($editing)
        <div style="display:flex;gap:8px">
          <button class="btn btn-light" style="flex:1" form="dup">Duplicate</button>
          <button class="btn btn-danger" style="flex:1" form="del">Delete</button>
        </div>
      @endif
    </div>
  </div>
</form>

@if($editing)
  <form id="dup" action="{{ route('admin.products.duplicate', $product) }}" method="post">@csrf</form>
  <form id="del" action="{{ route('admin.products.destroy', $product) }}" method="post" data-confirm="Delete this product? Past orders keep their details.">@csrf @method('DELETE')</form>
  @foreach($product->images as $img)
    <form id="del-img-{{ $img->id }}" action="{{ route('admin.products.image.delete', $img) }}" method="post" data-confirm="Remove this image?">@csrf @method('DELETE')</form>
  @endforeach
@endif
@endsection
