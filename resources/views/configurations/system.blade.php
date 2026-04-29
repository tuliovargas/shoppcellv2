@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        @if (session('success'))
            <div class="col-12">
                <div class="alert alert-success">
                    Configuração atualizada com sucesso.
                </div>
            </div>
        @endif
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar configurações</h3>
                </div>
                <form method="POST" enctype="multipart/form-data" action="{{route('configurations.system.update')}}">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <label for="company_name">Nome da Empresa</label>
                            <input value="{{$configuration->company_name}}" type="text" class="form-control" name="company_name" id="company_name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input value="{{$configuration->email}}" type="email" class="form-control" name="email" id="email">
                        </div>
                        <div class="form-group">
                            <label for="photo">Logo</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="photo" id="photo">
                                <label class="custom-file-label" for="photo">Selecione um arquivo</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cnpj">CNPJ</label>
                            <input value="{{$configuration->cnpj}}" data-inputmask="'mask': '99.999.999/9999-99'" type="text" class="form-control" name="cnpj" id="cnpj">
                        </div>
                        <div class="form-group">
                            <label for="cellphone">Telefone</label>
                            <input value="{{$configuration->cellphone}}" data-inputmask="'mask': '(99) 9999-9999'" type="text" class="form-control" name="cellphone" id="cellphone">
                        </div>
                        <div class="form-group">
                            <label for="instagram">
                                Instagram
                            </label>
                            <input class="form-control" value="{{$configuration->instagram}}" type="text" name="instagram" id="instagram">
                        </div>
                        <div class="form-group">
                            <label for="address">Endereço</label>
                            <input value="{{$configuration->address}}" type="text" class="form-control" name="address" id="address">
                        </div>
                        <div class="form-group">
                            <label for="bussiness_hours">Horário de Funcionamento</label>
                            <input value="{{$configuration->bussiness_hours}}" type="text" class="form-control" name="bussiness_hours" id="bussiness_hours">
                        </div>
                        <div class="form-group">
                            <label for="cupom_text">DDD Padrão</label>
                            <input value="{{$configuration->easy_ddd}}" type="text" class="form-control" name="easy_ddd" id="easy_ddd">
                        </div>
                        <div class="form-group">
                            <label for="easy_postcode">CEP Padrão</label>
                            <input value="{{$configuration->easy_postcode}}" data-inputmask="'mask': '99999-999'" type="text" class="form-control" name="easy_postcode" id="easy_postcode">
                        </div>
                        <div class="form-group">
                            <label for="cupom_text">Texto de Cupom</label>
                            <input value="{{$configuration->cupom_text}}" type="text" class="form-control" name="cupom_text" id="cupom_text">
                        </div>
                        <div class="form-group">
                            <label for="warranty_text">Texto de Garantia</label>
                            <input value="{{$configuration->warranty_text}}" type="text" class="form-control" name="warranty_text" id="warranty_text">
                        </div>
                        <div class="form-group">
                            <label for="maintenance_text">Texto de Manutenção</label>
                            <input value="{{$configuration->maintenance_text}}" type="text" class="form-control" name="maintenance_text" id="maintenance_text">
                        </div>
                        <div class="form-group">
                            <label for="qrcode_tip">Dica QRCode</label>
                            <input value="{{$configuration->qrcode_tip}}" type="text" class="form-control" name="qrcode_tip" id="qrcode_tip">
                        </div>
                        <div class="form-group">
                            <label for="orcamento_text">Texto de Orçamento</label>
                            <input value="{{$configuration->orcamento_text}}" type="text" class="form-control" name="orcamento_text" id="orcamento_text">
                        </div>
                        <div class="form-group">
                            <label for="budget">Orçamento</label>
                            <input value="{{$configuration->budget}}" type="text" class="form-control" name="budget" id="budget">
                        </div>
                        <div class="form-group">
                            <label for="secret_word">Palavra Secreta</label>
                            <input value="{{$configuration->secret_word}}" type="text" class="form-control" name="secret_word" id="secret_word">
                        </div>
                        <div class="form-group">
                            <label for="secret_word">Palavra Secreta Pedidos Antigos</label>
                            <input value="{{$configuration->secret_word_for_old_orders}}" type="text" class="form-control" name="secret_word_for_old_orders" id="secret_word_for_old_orders">
                        </div>
                        <div class="form-group">
                            <label for="msg_week-buyers">Mensagem Clientes com compras na Semana</label>
                            <input value="{{$configuration->{'msg_week-buyers'} }}" type="text" class="form-control" name="msg_week-buyers" id="msg_week-buyers">
                        </div>
                        <div class="form-group">
                            <label for="msg_birthdays">Mensagem aniversariantes do dia</label>
                            <input value="{{$configuration->msg_birthdays}}" type="text" class="form-control" name="msg_birthdays" id="msg_birthdays">
                        </div>
                        <div class="form-group">
                            <label for="msg_detached">Mensagem promoção avulsa</label>
                            <input value="{{$configuration->msg_detached}}" type="text" class="form-control" name="msg_detached" id="msg_detached">
                        </div>
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
