@extends('adminlte::page')

@section('title', 'Fornecedores')

@section('content_header')
<h1>Fornecedores</h1>
@stop

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Deletar Fornecedor - {{$supplier->name}}</h3>
        </div>
        <form method="POST" action="{{route('suppliers.destroy', ['supplier' => $supplier->id])}}">
          <!-- /.card-header -->
          <div class="card-body">
            Deseja realmente deletar o fornecedor - {{$supplier->name}}?
            <!-- /.card-body -->
          </div>
          <div class="card-footer">
            @csrf
            @method('delete')
            <a href="{{route('suppliers.index')}}" class="btn btn-danger" style="color: white!important">Cancelar</a>
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