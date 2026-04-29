@extends('adminlte::page')

@section('title', 'Despesas')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Despesas</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="expenses" class="table table-bordered table-hover mb-2" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Fatura</th>
                                <th>Parcelas</th>
                                <th>Valor</th>
                                @if (false)
                                <th>Ações</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($expenses))
                            @foreach ($expenses as $expense)
                            <tr>
                                <td>{{ $expense->id }}</td>
                                <td>{{ $expense->name }}</td>
                                <td>{{ $expense->invoice }}</td>
                                <td>{{ $expense->installments }}</td>
                                <td>R$ {{ number_format($expense->value, 2, ',', '.') }}</td>
                                @if (false)
                                <td>
                                    <a href="{{ route('expenses.edit', ['expense' => $expense->id]) }}">
                                        <i class="mx-2 fas fa-pen"></i>
                                    </a>
                                    <a href="{{ route('expenses.destroy', ['expense' => $expense->id]) }}">
                                        <i class="mx-2 fas fa-trash"></i>
                                    </a>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                            @endif
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
    $(document).ready(function() {
        $('#expenses').DataTable({
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