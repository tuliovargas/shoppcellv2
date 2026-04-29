@extends('adminlte::page')

@section('title', 'Fornecedores')

@section('content_header')
@stop

@section('content')
<supplier-form supplier-id='0'></supplier-form>
@stop
@section('plugins.InputMask', true)

@section('js')
<script src="{{ mix('js/module-supplier.js') }}"></script>
<script>
  $(":input").inputmask();
</script>
@stop