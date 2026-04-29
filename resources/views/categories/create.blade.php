@extends('adminlte::page')

@section('title', 'Categorias')

@section('content_header')
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cadastrar nova categoria</h3>
                </div>
                <form method="POST" enctype="multipart/form-data" action="{{route('categories.store')}}">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nome da Categoria</label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                        <div class="form-group">
                            <label for="photo_url">Foto</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="photo" id="photo">
                                <label class="custom-file-label" for="photo">Selecione um arquivo</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="parent_id">Categoria Principal</label>
                            <select name="parent_id" id="parent_id" class="form-control">
                                <option value="">Sem Categoria Principal</option>
                                @foreach ($parentCategories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <div class="card-footer ml-auto bg-white">
                            @csrf
                            <a href="{{route('categories.index')}}" class="btn btn-danger"
                                style="color: white!important">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4">Confirmar</button>
                        </div>
                    </div>
                </form>
                <!-- /.card -->
            </div>
        </div>
    </div>
    @stop

    @section('plugins.bscustomfileinput', true)

    @section('js')
    <script>
        $(document).ready(function() {
            bscustomfileinput.init();
        });
    </script>
    @stop