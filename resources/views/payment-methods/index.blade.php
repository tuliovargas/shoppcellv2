@extends('adminlte::page')

@section('title', 'Modelos')

@section('content_header')
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Métodos de Pagamento</h3>
                        <div class="card-tools">
                            <a href="{{route('payment-methods.create')}}">Cadastrar Novo Método de Pagamento</a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="brands"
                               class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($paymentMethods as $paymentMethod)
                                <tr>
                                    <td>{{ $paymentMethod->id }}</td>
                                    <td>{{ $paymentMethod->name }}</td>
                                    <td>
                                        <a href="{{ route('payment-methods.edit', $paymentMethod) }}">
                                            <i class="mx-2 fas fa-pen"></i>
                                        </a>
                                        <a href="{{ route('payment-methods.delete', ['payment_method' => $paymentMethod]) }}">
                                            <i class="mx-2 fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@stop

@section('plugins.Datatables', true)

@section('js')
    <script>
        $(function () {
            $('#brands').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@stop
