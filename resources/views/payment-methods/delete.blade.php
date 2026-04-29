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
            <h3 class="card-title">Deletar método de pagamento - {{$paymentMethod->name}}</h3>
          </div>
          <form method="POST" action="{{route('payment-methods.destroy', ['payment_method' => $->id])}}">
            <!-- /.card-header -->
            <div class="card-body">
                Deseja realmente deletar o método de pagamento - {{$paymentMethod->name}}?
              <!-- /.card-body -->
            </div>
            <div class="card-footer">
                @csrf
                @method('delete')
                <a href="{{route('payment-methods.index')}}" class="btn btn-primary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </form>
        <!-- /.card -->
      </div>
    </div>
</div>
@stop

@section('js')
@stop
