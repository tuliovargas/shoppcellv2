@extends('adminlte::page')

@section('title', 'Modelos')

@section('content_header')
@stop

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
                    <h3 class="card-title">Lista de Modelos</h3>
                    <div class="card-tools">
                        <a href="{{route('brand-models.create')}}">Cadastrar Novo Modelo</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="brands" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagem</th>
                                <th>Marca</th>
                                <th>Modelo</th>
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
<script>
    $(function () {
            $('#brands').DataTable({
                "pageLength": 100,
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "stripeClasses": ['bg-white', 'bg-light'],
                ajax: {
                    url: "{{ route('brand-models.index') }}?type=datatables",
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        searchable: true
                    },
                    {
                        data: 'photo_url',
                        name: 'photo_url',
                        searchable: false,
                    },
                    {
                        data: 'brand',
                        name: 'brand',
                        searchable: false,
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'action',
                        name: 'Ações',
                        searchable: false,
                    },
                ],
                "info": true,
                "serverSide": true,
                "autoWidth": true,
                "responsive": true,
            });
        });
</script>
@stop