@extends('layouts.sidenav-layout')
@section('content')
    @include('components.product.product-list')
    @include('components.product.product-delete')
    @include('components.product.product-create')

@endsection
