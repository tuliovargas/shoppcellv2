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
                    <h3 class="card-title">Deletar Usuário - {{$user->name}}</h3>
                </div>
                <form method="POST" action="{{route('users.destroy', ['user' => $user->id])}}">
                    <!-- /.card-header -->
                    <div class="card-body">
                        Deseja realmente deletar o usuário - {{$user->name}}?
                        <!-- /.card-body -->
                    </div>
                    <div class="card-footer">
                        @csrf
                        @method('delete')
                        <a href="{{route('users.index')}}" class="btn btn-primary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </form>
                <!-- /.card -->
            </div>
        </div>
    </div>
    @stop

    @section('plugins.Datatables', true)

    @section('js')
    @stop
