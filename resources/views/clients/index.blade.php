@extends('adminlte::page')

@section('title', 'Clientes')

@section('content')

<div class="container-fluid">
    <div class="row">
        @if (session('success') && session('success'))
        <div class="col-12">
            <div class="alert alert-success">
                {{session('message')}}
            </div>
        </div>
        @endif
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Clientes</h3>
                    <div class="card-tools">
                        <a href="{{route('clients.create')}}">Cadastrar Novo Cliente</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="clients" class="table table-bordered table-hover mb-2" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>CPF/CNPJ</th>
                                <th>Data de Nascimento</th>
                                <th>Celular</th>
                                <th>Status</th>
                                <th>Total Compras</th>
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
            ajax: "{{ route('clients.index') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    searchable: true
                },
                {
                    data: 'client',
                    name: 'full_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'cpf',
                    name: 'cpf'
                },
                {
                    data: 'birthdate',
                    name: 'birthdate'
                },
                {
                    data: 'cellphone',
                    name: 'cellphone'
                },
                {
                    data: 'is_active',
                    name: 'is_active'
                },
                {
                    data: 'total_purchases',
                    name: 'total_purchases'
                },
                {
                    data: 'action',
                    name: 'Ações',
                    searchable: false,
                    orderable: false
                },
            ],
            "searching": true,
            search: {
                "regex": true
            },
            "language": {
                search: 'Pesquisar'
            },
            "ordering": true,
            "processing": true,
            "serverSide": true,
            "autoWidth": true,
            "responsive": true,
            "scrollX": true,
            "order": [[ 0, "desc" ]],
        });
    });
</script>
@stop