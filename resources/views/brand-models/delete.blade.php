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
          <h3 class="card-title">Deletar Modelo - {{$brandModel->name}}</h3>
        </div>
        <form method="POST" action="{{route('brand-models.destroy', ['brand_model' => $brandModel->id])}}">
          <!-- /.card-header -->
          <div class="card-body">
            Deseja realmente deletar o modelo - {{$brandModel->name}}?
            <!-- /.card-body -->
          </div>
          <div class="card-footer">
            @csrf
            @method('delete')
            <a href="{{route('brand-models.index')}}" class="btn btn-danger" style="color:white!important">Cancelar</a>
            <button type="submit" class="btn btn-primary px-4">Confirmar</button>
          </div>
        </form>
        <!-- /.card -->
      </div>
    </div>
  </div>
  @stop

  @section('js')
  @stop