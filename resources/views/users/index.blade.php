@extends('adminlte::page')

@section('title', 'Usuários')

@section('content_header')
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Usuários</h3>
                    <div class="card-tools">
                        <a href="{{route('users.create')}}">Cadastrar Novo Usuário</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="users" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{$user->id}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    <a href="{{route('users.edit', ['user' => $user->id])}}">
                                        <i class="mx-2 fas fa-pen"></i>
                                    </a>
                                    <a href="{{route('users.delete', ['user' => $user->id])}}">
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
    $(function() {
        $('#users').DataTable({
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
