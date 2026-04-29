@extends('adminlte::page')

@section('title', 'Modelos')

@section('content_header')
@endsection

@section('content')
<div class="container mx-auto">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cadastrar novo método de pagamento</h3>
                </div>
                <form method="POST" action="{{ route('payment-methods.store') }}">
                    @csrf
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nome</label>
                            <input type="text" class="form-control" name="name" required id="name">
                        </div>
                        <div class="d-flex flex-row justify-content-between aling-items-center">
                            <div class="form-check ml-5 mt-1 ">
                            </div>
                            <div class="card-footer bg-white">
                                <a href="{{ route('payment-methods.index') }}" class="btn btn-danger"
                                    style="color: white!important">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4">Confirmar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- /.card -->
            </div>
        </div>
    </div>
</div>
@endsection
@section('plugins.Select2', true)

@section('js')
<script>
    $(document).ready(function () {
            $('.select2').select2();
        });
</script>
@endsection