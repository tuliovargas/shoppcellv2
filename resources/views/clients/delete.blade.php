@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
@stop

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Deletar Cliente - {{$client->full_name}}</h3>
        </div>
        <form method="POST" action="{{route('clients.destroy', ['client' => $client->id])}}">
          <!-- /.card-header -->
          <div class="card-body">
            Deseja realmente deletar o cliente - {{$client->full_name}}?
            <!-- /.card-body -->
          </div>
          <div class="card-footer">
            @csrf
            @method('delete')
            <a href="{{route('clients.index')}}" class="btn btn-danger" style="color:white !important">Cancelar</a>
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