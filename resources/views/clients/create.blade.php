@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
@stop

@section('content')
<div class="container mx-auto ">
  <div class="row ">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Cadastrar Novo Cliente</h3>
        </div>
        <form method="POST" enctype="multipart/form-data" action="/clients" autocomplete="no">
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              @if ($errors->all())
              <div class="col-12">
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                    <li>
                      {{$err}}
                    </li>
                    @endforeach
                  </ul>
                </div>
              </div>
              @endif
              <div class="col-2 mx-auto ">
                <div class="card ">
                  <img id="preview" src="/images/default-product.png" alt="" class="card-img-top w-100 ">
                  <div class="d-flex  border">
                    <label class="position-absolute d-flex align-self-center text-center justify-content-center">
                      <input type="file" class="d-flex  custom-file-input" name="photo_url" id="photo">
                      <span class="bg-dark bg-gradient-dark  w-100 position-absolute">editar</span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group col-12 ">
                  <label for="full_name">Nome</label>
                  <input type="text" class="form-control" required name="full_name" id="name">
                </div>
                <div class="form-group col-12 ">
                  <label for="social_name">Nome Social</label>
                  <input type="text" class="form-control" name="social_name" id="social_name">
                </div>
              </div>

            </div>

            <div class="row">
              <div class="form-group col-6">
                <label for="rg">RG/IE</label>
                <input data-inputmask="'mask': ['99.999.999-99', '##############################']" type="text"
                  class="form-control" name="rg" id="rg">
              </div>
              <div class="form-group col-6">
                <label for="cpf">CPF/CNPJ</label>
                <input data-inputmask="'mask': ['###.###.###-##', '##.###.###/####-##'] " type="text"
                  class="form-control" name="cpf" required id="cpf">
              </div>
            </div>

            <div class="row">
              <div class="form-group col-6">
                <label for="profession">Profissão</label>
                <input type="text" class="form-control" name="profession" id="profession">
              </div>
              <div class="form-group col-6">
                <label for="email">E-mail</label>
                <input type="email" class="form-control" name="email" id="email">
              </div>
            </div>

            <div class="row">
              <div class="form-group col-6">
                <label for="birthdate">Data de Nascimento</label>
                <input type="date" class="form-control" name="birthdate" id="birthdate">
              </div>
              <div class="form-group col-6">
                <label for="gender">Sexo</label>
                <select name="gender" id="gender" class="form-control">
                  <option value selected disabled> Sexo </option>
                  <option value="f">Feminino</option>
                  <option value="m">Masculino</option>
                  <option value="o">Outro</option>
                  <option value="">Prefiro não dizer</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-6">
                <label for="cellphone">Celular</label>
                <input data-inputmask="'mask': '(99) 99999-9999'" type="text" class="form-control" name="cellphone"
                  id="cellphone">
              </div>
              <div class="form-group col-6">
                <label for="phone">Telefone</label>
                <input data-inputmask="'mask': '(99) 99999-9999'" type="text" class="form-control" name="phone"
                  id="phone">
              </div>
            </div>

            <div class="row">
              <div class="form-group col-12 ">
                <label class="" for="observation">Observação</label>
                <input type="text" class="form-control h-100" name="observation" id="observation">
              </div>

            </div>
            <h5 class="mt-5">Endereço </h5>
            <hr>
            <div class="row">
              <div class="form-group col-2">
                <label for="postcode">CEP</label>
                <input v-mask="'#####-###'" data-inputmask="'mask': '99999-999'" type="text" class="form-control"
                  name="postcode" id="postcode">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-9">
                <label for="street">Rua/Av.</label>
                <input type="text" class="form-control" name="street" id="street">
              </div>
              <div class="form-group col-md-3">
                <label for="number">Numero</label>
                <input type="text" class="form-control" name="number" id="number">
              </div>
            </div>


            <div class="row">
              <div class="form-group col-6">
                <label for="neighborhood">Bairro</label>
                <input type="text" class="form-control" name="neighborhood" id="neighborhood">
              </div>

              <div class="form-group col-6">
                <label for="complement">Complemento (Opcional)</label>
                <input type="text" class="form-control" name="complement" id="complement">
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-2">
                <label for="state">Estado</label>
                <select name="state" id="state" class="form-control">
                  @include('utils.states')
                </select>
              </div>
              <div class="form-group col-md-10">
                <label for="city">Cidade</label>
                <input type="text" class="form-control" name="city" id="city">
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <hr>
          <div class="d-flex flex-row justify-content-between aling-items-center">
            <div class="form-check ml-5 mt-1 ">
              <input type="checkbox" class="form-check-input" checked id="is_active" name="is_active">
              <label class="form-check-label" for="is_active">Ativo</label>
            </div>
            <div class="card-footer  bg-white">
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
  @section('plugins.InputMask', true)

  @section('js')
  <script src="/js/helpers/image.js"></script>
  <script>
    $(":input").inputmask();
    $(document).ready(function () {
       
        $("#photo").on('change', function(e){
            if(e.target.files && e.target.files[0]){
                preview(e.target.files[0], $('#preview').get()[0], 'image')
            }else{
                $('#preview').get()[0].src= '/images/default-product.png'
            }
        })
    });
    $("#postcode").blur(function() {
         var postcode = $(this).val().replace(/\D/g, '');
        if (postcode.length === 8) {
            $("#street").val("...");
            $("#neighborhood").val("...");
            $("#city").val("...");
            $("#state").val("...");

            $.getJSON("https://viacep.com.br/ws/"+ postcode +"/json/?callback=?", function(data) {
                if (!("erro" in data)) {
                    $("#street").val(data.logradouro);
                    $("#neighborhood").val(data.bairro);
                    $("#city").val(data.localidade);
                    $("#state").val(data.uf);
                    $("#complement").val(data.complemento)
                }
            });
        }
    });

  </script>
  @stop