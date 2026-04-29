@extends('adminlte::page')

@section('title', 'Usuários')

@section('content_header')
@stop

@section('content')
<div class="container mx-auto">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cadastrar Novo Usuário</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                <form method="POST" enctype="multipart/form-data" action="{{ route('users.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-2 mx-auto ">
                            <div class="card ">
                                <img id="blah" src="/images/default-avatar.png" alt="" class="card-img-top w-100 ">
                                <div class="d-flex  border">
                                    <label class="position-absolute d-flex align-self-center text-center  justify-content-center">
                                        <input type="file" class="d-flex  custom-file-input" name="photo" id="photo">
                                        <span class="bg-dark bg-gradient-dark  w-100 position-absolute">editar</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-10">
                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" name="name" id="name" autofocus>
                            </div>
                            <div class="form-group">
                                <label for="role">Função</label>
                                <select name="role" id="role" class="form-control">
                                    @foreach ($roles as $role)
                                    <option value="{{$role->name}}">{{ucfirst($role->name)}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>


                    <div class="row">

                        <div class="form-group col-6">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" name="email" id="email">
                        </div>
                        <div class="form-group col-6">
                            <label for="password">Senha</label>
                            <input type="password" class="form-control" name="password" id="password">
                        </div>

                    </div>
                    <hr>
                    <div class="d-flex flex-row justify-content-between aling-items-center">
                        <div class="form-check ml-5 mt-1 ">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active">
                            <label class="form-check-label" for="is_active">Ativo</label>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{route('users.index')}}" class="btn btn-danger" style="color: white!important">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4">Confirmar</button>
                        </div>
                    </div>
                    <!-- /.card -->
                </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('plugins.Datatables', true)

@section('js')
    <script>
        function readURL(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
              $('#blah').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]); // convert to base64 string
          }
        }

        $("#photo").change(function() {
          readURL(this);
        });
    </script>
@stop
