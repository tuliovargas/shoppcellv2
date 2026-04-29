@extends('adminlte::page')

@section('title', 'Marcas')

@section('content_header')
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        @if (session('success'))
        <div class="col-12">

            @switch(session('success'))
            @case(true)
            <div class="alert alert-success">
                {{session('message')}}
            </div>
            @break
            @case(false)
            <div class="alert alert-danger">
                {{session('message')}}
            </div>
            @break
            @endswitch
        </div>
        @endif
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Marcas</h3>
                    <div class="card-tools">
                        <a href="{{route('brands.create')}}">Cadastrar Nova Marca</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="brands" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th style="width: 120px">Foto</th>
                                <th>Nome</th>

                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $brand)
                            <tr>
                                <td>{{$brand->id}}</td>

                                <td align="center">
                                    <img height="40px" width="auto" src="{{ url('storage/'. $brand->photo_url) }}"
                                        alt="">
                                    @if ($brand->photo_url)
                                    <img width="48" height="48" src="{{ asset('storage/'. $brand->photo_url) }}">
                                </td>
                                @else
                                <img width="48" height="48" src="{{ asset('images/default-image.png') }}">
                                </td>
                                @endif
                                </td>

                                <td>{{$brand->name}}</td>

                                <td>
                                    <a href="{{route('brands.edit', ['brand' => $brand->id])}}">
                                        <i class="mx-2 fas fa-pen"></i>
                                    </a>
                                    <a href="{{route('brands.delete', ['brand' => $brand->id])}}">
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
        $('#brands').DataTable({
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