@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
@stop

@section('content')
<div class="container mx-auto">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar cliente - {{ $client->full_name }}</h3>
                </div>
                <form method="POST" action="{{ route('clients.update', ['client' => $client->id]) }}">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-2">
                                        <img id="preview"
                                            src="{{ !empty($client->photo_url) ? '/storage/'.$client->photo_url : '/images/default-product.png'}}"
                                            alt="" class="card-img-top w-100 ">
                                        <div class="d-flex border">
                                            <label
                                                class="position-absolute d-flex align-self-center text-center justify-content-center">
                                                <input type="file" class="d-flex custom-file-input" name="photo_url"
                                                    id="photo">
                                                <span
                                                    class="bg-dark bg-gradient-dark w-100 position-absolute">editar</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <div class="form-group">
                                            <label for="full_name">Nome</label>
                                            <input value="{{ $client->full_name }}" type="text" class="form-control"
                                                name="full_name" id="name">
                                        </div>
                                        <div class="form-group">
                                            <label for="social_name">Nome Social</label>
                                            <input value="{{ $client->social_name }}" type="text" class="form-control"
                                                name="social_name" id="social_name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row">
                                    <div class="form-group col-12 col-md-6">
                                        <label for="cpf">CPF/CNPJ</label>
                                        <input value="{{ $client->cpf }}"
                                            data-inputmask="'mask': ['999.999.999-99', '99.999.999/9999-99'] "
                                            type="text" class="form-control" name="cpf" id="cpf">
                                    </div>
                                    <div class="form-group col-12 col-md-6">
                                        <label for="rg">RG/IE</label>
                                        <input value="{{ $client->rg }}"
                                            data-inputmask="'mask': ['99.999.999-99', '999999999999999999999999999999']"
                                            type="text" class="form-control" name="rg" id="rg">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12 col-md-6">
                                        <label for="email">E-mail</label>
                                        <input value="{{ $client->email }}" type="email" class="form-control"
                                            name="email" id="email">
                                    </div>
                                    <div class="form-group col-12 col-md-6">
                                        <label for="gender">Sexo</label>
                                        <select name="gender" id="gender" class="form-control">
                                            <option {{ ( $client->gender === 'f') ? 'selected' : '' }} value="f">
                                                Feminino
                                            </option>
                                            <option {{ ( $client->gender === 'm') ? 'selected' : '' }} value="m">
                                                Masculino
                                            </option>
                                            <option {{ ( $client->gender === 'o') ? 'selected' : '' }} value="o">Outro
                                            </option>
                                            <option {{ ( $client->gender === '') ? 'selected' : '' }} value="">Prefiro
                                                não
                                                dizer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12 col-md-6">
                                        <label for="profession">Profissão</label>
                                        <input value="{{ $client->profession }}" type="text" class="form-control"
                                            name="profession" id="profession">
                                    </div>
                                    <div class="form-group col-12 col-md-6">
                                        <label for="birthdate">Data de Nascimento</label>
                                        <input value="{{ $client->birthdate }}" type="date" class="form-control"
                                            name="birthdate" id="birthdate">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-12 col-md-6">
                                        <label for="cellphone">Celular</label>
                                        <input value="{{ $client->cellphone }}"
                                            data-inputmask="'mask': '(99) 9999-9999' " type="text" class="form-control"
                                            name="cellphone" id="cellphone">
                                    </div>
                                    <div class="form-group col-12 col-md-6">
                                        <label for="phone">Telefone</label>
                                        <input value="{{ $client->phone }}" data-inputmask="'mask': '(99) 99999-9999'"
                                            type="text" class="form-control" name="phone" id="phone">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="observation">Observação</label>
                                    <input value="{{ $client->observation }}" type="text" class="form-control"
                                        name="observation" id="observation">
                                </div>
                                <div class="form-check">
                                    <input checked="{{ $client->is_active }}" type="checkbox" class="form-check-input"
                                        id="is_active" name="is_active">
                                    <label class="form-check-label" for="is_active">Ativo</label>
                                </div>
                                <h5 class="mt-2">Endereço</h5>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="postcode">CEP</label>
                                        <input value="{{ $client->address ? $client->address->postcode : '' }}"
                                            data-inputmask="'mask': '99999-999'" type="text" class="form-control"
                                            name="postcode" id="postcode">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="street">Rua/Av.</label>
                                        <input value="{{ $client->address ? $client->address->street : '' }}"
                                            type="text" class="form-control" name="street" id="street">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="neighborhood">Bairro</label>
                                        <input value="{{ $client->address ? $client->address->neighborhood : '' }}"
                                            type="text" class="form-control" name="neighborhood" id="neighborhood">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="number">Numero</label>
                                        <input value="{{ $client->address ? $client->address->number : '' }}"
                                            type="text" class="form-control" name="number" id="number">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="complement">Complemento (Opcional)</label>
                                        <input value="{{ $client->address ? $client->address->complemet : '' }}"
                                            type="text" class="form-control" name="complement" id="complement">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="city">Cidade</label>
                                        <input value="{{ $client->address ? $client->address->city : ''}}" type="text"
                                            class="form-control" name="city" id="city">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="state">Estado</label>
                                        <select name="state" id="state" class="form-control">
                                            @include('utils.states')
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- /.card-body -->
                    </div>
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <div class="card-footer ml-auto bg-white">
                            @csrf
                            @method('put')
                            <a href="{{ route('clients.index') }}" class="btn btn-danger"
                                style="color: white!important">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4">Editar
                            </button>
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
        $(document).ready(function () {
            $("#state").val("{{$client->address ? $client->address->state : ''}}")
        $("#photo").on('change', function(e){
            if(e.target.files && e.target.files[0]){
                preview(e.target.files[0], $('#preview').get()[0], 'image')
            }else{
                $('#preview').get()[0].src= '/images/default-product.png'
            }
        })
    });
        $(":input").inputmask();
                $("#postcode").blur(function () {
                    var postcode = $(this).val().replace(/\D/g, '');
                    console.log(postcode)
                    if (postcode.length === 8) {
                        $("#street").val("...");
                        $("#neighborhood").val("...");
                        $("#city").val("...");
                        $("#state").val("...");

                        $.getJSON("https://viacep.com.br/ws/" + postcode + "/json/?callback=?", function (data) {
                            console.log(data)
                            if (!("erro" in data)) {
                                $("#street").val(data.logradouro);
                                $("#neighborhood").val(data.bairro);
                                $("#city").val(data.localidade);
                                $("#state").val(data.uf);
                            }
                        });
                    }
                });
    </script>
    @stop