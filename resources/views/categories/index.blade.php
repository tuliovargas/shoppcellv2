@extends('adminlte::page')

@section('title', 'Categorias')

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
                    <h3 class="card-title">Lista de Categorias</h3>
                    <div class="card-tools">
                        <a href="{{route('categories.create')}}">Cadastrar nova categoria</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="users" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagem</th>
                                <th>Nome</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                            <tr>
                                <td>{{$category->id}}</td>
                                <td>
                                    @if ($category->photo_url)
                                    <img width="48" height="48" src="{{ asset('storage/'. $category->photo_url) }}">
                                </td>
                                @else
                                <img width="48" height="48" src="{{ asset('images/default-image.png') }}">
                                </td>
                                @endif
                                </td>
                                <td>{{$category->parent ? $category->parent->name . " >> " : ''}} {{$category->name}}
                                </td>

                                <td>
                                    <a href="{{route('categories.edit', ['category' => $category->id])}}">
                                        <i class="mx-2 fas fa-pen"></i>
                                    </a>
                                    <a href="{{route('categories.delete', ['category' => $category->id])}}">
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
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
@stop