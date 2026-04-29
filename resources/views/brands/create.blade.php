@extends('adminlte::page')

@section('title', 'Marcas')

@section('content_header')
@stop

@section('content')
<div class="container mx-auto">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cadastrar nova Marca</h3>
                </div>
                <form method="POST" enctype="multipart/form-data" action="{{route('brands.store')}}">
                    @csrf
                    <!-- /.card-header -->
                    <div class="card-body">

                        <div class="row">

                            <div class="col-2">
                                <div class="card ">
                                    <img id="preview" src="/images/default-product.png" alt=""
                                        class="card-img-top w-100 ">
                                    <div class="d-flex  border">
                                        <label
                                            class="position-absolute d-flex align-self-center text-center  justify-content-center">
                                            <input type="file" class="d-flex  custom-file-input" name="photo_url"
                                                id="photo">
                                            <span
                                                class="bg-dark bg-gradient-dark  w-100 position-absolute">editar</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-10">
                                <div class="form-group">
                                    <label>Marcas</label>
                                </div>
                                <div class="form-group">
                                    <label for="name">Nome da Marca</label>
                                    <input type="text" class="form-control" name="name" id="name">
                                </div>
                            </div>

                        </div>
                        <hr>
                        <div class="d-flex flex-row justify-content-between aling-items-center">
                            <div class="form-check ml-5 mt-1 ">
                            </div>
                            <div class="card-footer  bg-white">

                                <a href="{{route('brands.index')}}" class="btn btn-danger"
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

    @section('plugins.Select2', true)

    @section('js')
    <script src="/js/helpers/image.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            $("#photo").on('change', function(e){
                if(e.target.files && e.target.files[0]){
                    preview(e.target.files[0], $('#preview').get()[0], 'image')
                }else{
                    $('#preview').get()[0].src= '/images/default-product.png'
                }
            })
        });

        $('#discount_input').hide();
        $('#commission_input').hide();

        $('#can_discount').click(function() {
            if ($(this).is(':checked')) {
                $('#discount_input').show();
            } else {
                $('#discount_input').hide();
            }
        });

        $('#can_commission').click(function() {
            if ($(this).is(':checked')) {
                $('#commission_input').show();
            } else {
                $('#commission_input').hide();
            }
        });
    </script>
    @stop