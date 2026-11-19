@extends('layouts.admin')
@php use App\Support\Shop; $title = 'Products'; $low = (int) Shop::get('low_stock', 5); @endphp
@section('actions')
  <a class="btn" href="{{ route('admin.products.create') }}"><svg class="ico ico-sm"><use href="#a-plus"/></svg><span class="hide-sm">Add product</span></a>
@endsection

@section('content')
<div class="panel">
  <form class="filters">
    <input class="input grow" name="q" value="{{ request('q') }}" placeholder="Search by name or SKU">
    <select class="select" name="category" style="width:auto">
      <option value="">All categories</option>
      @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select class="select" name="stock" style="width:auto">
      <option value="">Any stock</option><option value="low" @selected(request('stock') === 'low')>Low stock</option><option value="out" @selected(request('stock') === 'out')>Sold out</option>
    </select>
    <select class="select" name="status" style="width:auto">
      <option value="">Any status</option><option value="featured" @selected(request('status') === 'featured')>Featured</option><option value="hidden" @selected(request('status') === 'hidden')>Hidden</option>
    </select>
    <button class="btn">Filter</button>
  </form>
  <div class="table-wrap">
    <table class="t responsive">
      <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Sold</th><th>Status</th><th class="num">Actions</th></tr></thead>
      <tbody>
        @forelse($products as $p)
          <tr>
            <td class="full"><a class="cell-prod" href="{{ route('admin.products.edit', $p) }}"><img class="thumb" src="{{ Shop::img($p->image, 120) }}" alt=""><span><b>{{ $p->name }}</b><span class="muted small">{{ $p->sku }}</span></span></a></td>
            <td data-label="Category">{{ $p->category?->name ?? '—' }}</td>
            <td data-label="Price"><b>{{ Shop::money($p->price) }}</b>@if($p->discount_percent)<div class="muted small"><s>{{ Shop::money($p->compare_price) }}</s> −{{ $p->discount_percent }}%</div>@endif</td>
            <td data-label="Stock"><span class="badge {{ $p->stock == 0 ? 'b-bad' : ($p->stock <= $low ? 'b-pending' : 'b-ok') }}">{{ $p->stock == 0 ? 'Sold out' : $p->stock.' in stock' }}</span></td>
            <td data-label="Sold">{{ $p->sold }}</td>
            <td data-label="Status">
              @if(! $p->is_active)<span class="badge">Hidden</span>@else<span class="badge b-ok">Live</span>@endif
              @if($p->is_featured)<span class="badge b-shipped">Featured</span>@endif
            </td>
            <td data-label="Actions" class="num">
              <div style="display:inline-flex;gap:6px">
                <a class="btn btn-light btn-sm" href="{{ route('admin.products.edit', $p) }}">Edit</a>
                <a class="btn btn-light btn-sm" href="{{ route('product', $p) }}" target="_blank" title="View in store"><svg class="ico ico-sm"><use href="#a-external"/></svg></a>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="empty full">No products found. <a href="{{ route('admin.products.create') }}" style="color:var(--accent)">Add your first product →</a></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $products->links('partials.pager') }}
</div>
@endsection
