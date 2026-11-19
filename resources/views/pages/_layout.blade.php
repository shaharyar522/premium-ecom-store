@extends('layouts.store')
@section('content')
<section class="page-head"><div class="wrap"><span class="eyebrow">{{ $eyebrow ?? 'Information' }}</span><h1 class="h1" style="margin-top:12px">{{ $title }}</h1></div></section>
<section class="wrap" style="padding-bottom:40px"><div class="prose">@yield('page')</div></section>
@endsection
