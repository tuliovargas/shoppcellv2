@extends('adminlte::page')

@section('title', 'Produto')

@section('content')
<product-form cancel-link="{{route('products.index')}}" product-id="{{ $product->id }}"/>
@stop

@section('plugins.Select2', true)

@section('js')
<script src="{{ mix('js/module-products.js') }}"></script>
<script src=""></script>
@stop
