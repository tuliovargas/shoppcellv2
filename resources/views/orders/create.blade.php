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
<style>
    .order-create {
        overflow-x: hidden;
        padding-bottom: 8px;
    }
    .order-create > .card-deck {
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        height: calc(100vh - 270px) !important;
    }
    .order-create .card-deck .cw-70,
    .order-create .card-deck .cw-30 {
        flex-shrink: 1 !important;
        min-width: 0;
    }
    .order-create .cw-70 > .card {
        min-width: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .order-create .cw-70 > .card > .card-body {
        flex: 1 1 auto;
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: visible;
    }
    .order-create .cw-70 .table-responsive {
        flex: 1 1 auto;
        min-width: 0;
        min-height: 0;
        max-width: 100%;
        height: auto !important;
        overflow: auto !important;
    }
    .order-create .table-orders {
        table-layout: fixed;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
    }
    .order-create .th__prod-name,
    .order-create .th__prod-amount,
    .order-create .th__prod-price,
    .order-create .th__prod-addition,
    .order-create .th__prod-discount,
    .order-create .th__prod-subtotal {
        min-width: 0 !important;
    }
</style>
@endsection
