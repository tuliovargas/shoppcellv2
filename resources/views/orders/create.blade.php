@extends('adminlte::page')

@section('title', 'Pedidos')

@section('content')
    <order-create :user="{{ auth()->user() }}"></order-create>
@endsection

@section('plugins.InputMask', true)
@section('js')
<script>
    $(":input").inputmask();
</script>
<script src="{{asset('js/app.js')}}"></script>
@endsection
