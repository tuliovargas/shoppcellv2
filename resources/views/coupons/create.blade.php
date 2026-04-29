@extends('adminlte::page')

@section('title', 'Cupons')

@section('content_header')
@stop

@section('content')
<div class="container mx-auto">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cadastrar novo cupom</h3>
                </div>
                <form method="POST" action="{{route('coupons.store')}}">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col">
                                <label for="barcode">Código do cupom</label>
                                <input type="text" required class="form-control" name="name" id="name">
                            </div>
                            <div class="form-group col">
                                <label for="price">Valor</label>
                                <input type="text" required class="form-control" name="value" id="value">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Inicio</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input required type="date" class="form-control" name="start_date">
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Encerramento</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input required type="date" class="form-control" name="end_date">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-3">
                                <label for="minimum_stock">Quantidade</label>
                                <input required type="number" class="form-control" name="quantity" id="quantity">
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <hr>
                    <div class="d-flex flex-row justify-content-between aling-items-center">
                        <div class="card-footer ml-auto bg-white">
                            @csrf
                            <a href="{{route('clients.index')}}" class="btn btn-danger" style="color: white!important">Cancelar</a>
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
    @section('plugins.InputMask', true)

    @section('js')
    <script>
        $(":input").inputmask();
        const money = new Inputmask({
            alias: 'currency',
            prefix: 'R$ ',
            radixPoint: ',',
            groupSeparator: '.',
            numericInput: true
        })
        let valueField = document.getElementById('value');
        money.mask(valueField)

        $(document).ready(function() {
            bscustomfileinput.init();
        });
    </script>
    @stop
