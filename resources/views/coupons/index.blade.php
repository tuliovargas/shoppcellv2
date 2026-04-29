@extends('adminlte::page')

@section('title', 'Cupons')

@section('content')

<div class="container-fluid">
    <div class="row">

        @if (session('success'))
        <div class="col-12">
            <div class="alert alert-success">
                {{session('message')}}
            </div>
        </div>

        @endif
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Cupons</h3>
                    <div class="card-tools">
                        <a href="{{route('coupons.create')}}">Cadastrar novo cupom</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="clients" class="table table-bordered table-hover mb-2" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Quantidade</th>
                                <th>Valor</th>
                                <th>Inicio</th>
                                <th>Encerramento</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
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
<script src="{{ mix('js/module-clients.js') }}"></script>

<script>
    $(document).ready(function() {
        console.log("ready!");
        $('#clients').DataTable({
            "paging": true,
            "lengthChange": false,
            "stripeClasses": ['bg-white', 'bg-light'],
            ajax: {
                url: "{{ route('coupons.index') }}?type=datatables",
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    searchable: true
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'quantity',
                    name: 'quantity',
                    searchable: false,
                },
                {
                    data: 'value',
                    name: 'value',
                    searchable: false,
                },
                {
                    data: 'start_date',
                    name: 'start_date',
                    searchable: false,
                },
                {
                    data: 'end_date',
                    name: 'end_date',
                    searchable: false,
                },
                {
                    data: 'action',
                    name: 'Ações',
                    searchable: false,
                },
            ],
            "searching": true,
            search: {
                "regex": true
            },
            "language": {
                search: 'Pesquisar',
            },
            "ordering": true,
            "processing": true,
            "serverSide": true,
            "autoWidth": true,
            "responsive": true,
            "scrollX": true
        });
    });
</script>
@stop