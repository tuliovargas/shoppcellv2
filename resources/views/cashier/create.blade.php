@extends('adminlte::page')

@section('title', 'Caixa')

@section('content')

<!-- Listar Fornecedores {{$suppliers}} -->
<!-- Listar Metodos de Pagamento {{$paymentMethod}} -->
<!-- Rota para cadastrar cashier {{ route('cashier.store') }}-->
<cashier-payment logged-user="{{\Illuminate\Support\Facades\Auth::user()}}" user-is-admin="{{\Illuminate\Support\Facades\Auth::user()->hasRole('admin')}}"></cashier-payment>
@stop

@section('plugins.Select2', true)

@section('js')
<!-- <script src="{{ mix('js/module-cashier.js') }}"></script> -->
<script src="{{ App\Helpers::file_version(mix('/js/module-cashier.js') )}}"></script>
@stop
