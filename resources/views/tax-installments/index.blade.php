
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
                    <h3 class="card-title">Editar Parcelamentos</h3>
                </div>
                <form method="POST" action="{{route('configurations.tax-installments.update')}}">
                    <!-- /.card-header -->
                    <div class="card-body row">
                        @foreach ($taxInstallments as $taxInstallment)
                        <div class="form-group col-lg-3 col-12">
                            <label for="name">{{$taxInstallment->quantity}}x</label>
                            <input
                            value="{{$taxInstallment->interest_rates}}"
                            type="text" class="form-control"
                            name="tax_installments[{{$taxInstallment->quantity}}]"
                            >
                        </div>
                        @endforeach
                        <!-- /.card-body -->
                    </div>
                    <div class="card-footer">
                        @csrf
                        @method('put')
                        <button type="submit" class="btn btn-primary">Editar</button>
                    </div>
                </form>
                <!-- /.card -->
            </div>
        </div>
    </div>
    @stop

    @section('plugins.InputMask', true)

    @section('js')
    <script>
        $(":input").inputmask();
    </script>
    @stop
