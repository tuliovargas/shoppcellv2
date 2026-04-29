@extends('adminlte::page')

@section('title', 'Fornecedores')

@section('content_header')
@stop

@section('content')
<supplier-form supplier-id="{{$supplier->id}}"></supplier-form>
@stop

@section('plugins.InputMask', true)

@section('js')
<script src="{{ mix('js/module-supplier.js') }}"></script>
@stop
