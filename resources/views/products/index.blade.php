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
                                @if (!empty($products))
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>
                                           @if ($product->brandModel && !empty($product->brandModel->photo_url))
                                            <img width="48" height="48" src="{{ asset('storage/' . $product->brandModel->photo_url) }}">
                                            </td>
                                            @else
                                            <img width="48" height="48" src="{{ asset('images/default-image.png') }}">
                                            </td>
                                            @endif
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        <a href="{{ route('products.edit', ['product' => $product->id]) }}">
                                            <i class="mx-2 fas fa-pen"></i>
                                        </a>
                                        <a href="{{ route('products.delete', ['product' => $product->id]) }}">
                                            <i class="mx-2 fas fa-trash"></i>
                                        </a>
                                    </td>
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
        $(function() {
            $('#users').DataTable({
                "paging": true,
                "pageLength": 100,
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
