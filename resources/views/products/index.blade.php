@extends('adminlte::page')

@section('title', 'Produtos')

@section('content_header')
    <h1>Produtos</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Produtos</h3>
                        <div class="card-tools">
                            <a href="{{ route('products.create') }}">Cadastrar novo produto</a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="users" class="table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Imagem</th>
                                    <th>Nome</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
        $(function() {
            $('#users').DataTable({
                paging: true,
                pageLength: 100,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('products.index') }}",
                order: [[0, 'asc']],
                language: {
                    search: 'Pesquisar',
                    processing: 'Carregando...',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ produtos',
                    infoEmpty: 'Nenhum produto',
                    zeroRecords: 'Nenhum produto encontrado',
                    paginate: {
                        previous: 'Anterior',
                        next: 'Próximo'
                    }
                },
                columns: [
                    { data: 'id', name: 'products.id' },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'products.name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

    </script>
@stop
