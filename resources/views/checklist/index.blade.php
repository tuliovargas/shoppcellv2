@extends('adminlte::page')

@section('title', 'Checklists')

@section('content_header')
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Checklists</h3>
                        <div class="card-tools">
                            <a href="{{route('configurations.checklists.create')}}">Cadastrar Novo Checklist</a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="brands"
                               class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Modelo</th>
                                <th>Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($checklists as $checklist)
                                <tr>
                                    <td>{{ $checklist->id }}</td>
                                    <td>{{ $checklist->name }}</td>
                                    <td>
                                        <a href="{{ route('configurations.checklists.edit', $checklist) }}">
                                            <i class="mx-2 fas fa-pen"></i>
                                        </a>
                                        <a href="{{ route('configurations.checklists.delete', ['checklist' => $checklist]) }}">
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
                "paging": false,
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
