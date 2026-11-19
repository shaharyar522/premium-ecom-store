@extends('layouts.admin')
@php use App\Support\Shop; $title = 'Categories'; @endphp
@section('actions')
  <a class="btn" href="{{ route('admin.categories.create') }}"><svg class="ico ico-sm"><use href="#a-plus"/></svg><span class="hide-sm">Add category</span></a>
@endsection

@section('content')
<div class="panel">
  <div class="table-wrap">
    <table class="t responsive">
      <thead><tr><th>Category</th><th>Products</th><th>Order</th><th>Status</th><th class="num">Actions</th></tr></thead>
      <tbody>
        @forelse($categories as $c)
          <tr>
            <td class="full"><a class="cell-prod" href="{{ route('admin.categories.edit', $c) }}"><img class="thumb" src="{{ Shop::img($c->image, 120) }}" alt=""><span><b>{{ $c->name }}</b><span class="muted small">{{ $c->tagline }}</span></span></a></td>
            <td data-label="Products"><a href="{{ route('admin.products.index', ['category' => $c->id]) }}">{{ $c->products_count }}</a></td>
            <td data-label="Order">{{ $c->sort }}</td>
            <td data-label="Status">@if($c->is_active)<span class="badge b-ok">Visible</span>@else<span class="badge">Hidden</span>@endif</td>
            <td data-label="Actions" class="num">
              <div style="display:inline-flex;gap:6px">
                <a class="btn btn-light btn-sm" href="{{ route('admin.categories.edit', $c) }}">Edit</a>
                <form action="{{ route('admin.categories.destroy', $c) }}" method="post" data-confirm="Delete {{ $c->name }}? Its products stay, without a category.">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Delete</button></form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="empty full">No categories yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
