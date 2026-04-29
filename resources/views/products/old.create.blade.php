@extends('adminlte::page')

@section('title', 'Produtos')

@section('content')
<div class="container mx-auto">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cadastrar novo produto</h3>
                </div>
                <form method="POST" enctype="multipart/form-data" action="{{ route('products.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Categorias</label>
                                    <select id="categories" name="categories[]" class="select2" multiple="multiple" data-placeholder="Selecione as Categorias" style="width: 100%;">
                                        @foreach ($categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                        @foreach ($category->children as $subcategory)
                                        <option value="{{$subcategory->id}}"> -- {{$subcategory->name}}</option>
                                        @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Nome do Produto</label>
                                    <input type="text" required class="form-control" name="name" id="name">
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label>Marca do produto</label>
                                    <select id="brands" name="brand_id" class="select2" data-placeholder="Selecione a Marca do produto" style="width: 100%;">
                                        @foreach ($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="photo">Foto</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="photo" id="photo">
                                        <label class="custom-file-label" for="photo">Selecione um arquivo</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="barcode">Código de Barra</label>
                                    <input type="text" class="form-control" name="barcode" id="barcode">
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="price">Valor</label>
                                    <input type="text" class="form-control" name="price" required id="price">
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="cost">Custo</label>
                                    <input type="text" class="form-control" name="cost" id="cost">
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="days_warranty">Dias de garantia</label>
                                    <input type="number" class="form-control" name="days_warranty" required id="days_warranty">
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="minimum_stock">Estoque Minimo</label>
                                    <input type="number" class="form-control" name="minimum_stock" required id="minimum_stock">
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="type">Tipo de Medida</label>
                                    <select name="type" id="type" required class="form-control">
                                        <option value="">Selecione</option>
                                        <option value="un">Unidade</option>
                                        <option value="sv">Serviço</option>
                                        <option value="kg">Kg</option>
                                        <option value="outro">Outro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="observation">Observação</label>
                                    <input type="text" class="form-control" name="observation" id="observation">
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="can_discount" value="1" name="can_discount">
                                    <label class="form-check-label" for="can_discount">Aceita Desconto?</label>
                                </div>
                                <div class="form-group mt-3" id="discount_percentage">
                                    <label for="discount_percentage_input">Desconto máximo (%)</label>
                                    <input type="number" id="discount_percentage_input" class="form-control" name="discount_percentage">
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="can_commission" value="1" name="can_commission">
                                    <label class="form-check-label" for="can_commission">Aceita Comissão?</label>
                                </div>
                                <div class="form-group mt-3" id="commission_percentage">
                                    <label for="commission_percentage_input">Comissão (%)</label>
                                    <input type="number" class="form-control" name="commission_percentage" id="commission_percentage">
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_new" value="1" name="is_new">
                                    <label class="form-check-label" for="is_new">É Novo?</label>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-check">
                                    <input type="checkbox" value="1" class="form-check-input" id="is_active" name="is_active">
                                    <label class="form-check-label" for="is_active">Ativo</label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <hr>
                    <div class="d-flex flex-row justify-content-between aling-items-center">
                        <div class="card-footer  bg-white">
                            <a href="{{ route('products.index') }}" class="btn btn-primary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Confirmar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('plugins.bscustomfileinput', true)
@section('plugins.Select2', true)
@section('plugins.InputMask', true)

@push('js')
<script>
    const money = new Inputmask({
        alias: 'currency',
        prefix: '',
        radixPoint: ',',
        groupSeparator: '.',
        numericInput: true
    })

    let priceField = document.getElementById('price');
    money.mask(priceField)

    let costField = document.getElementById('cost');
    money.mask(costField)

    $(document).ready(function() {
        $('.select2').select2();
    });

    $('#discount_percentage').hide();

    $('#can_discount').click(function() {
        if ($(this).is(':checked')) {
            $('#discount_percentage').show();
        } else {
            $('#discount_percentage').hide();
        }
    });

    $('#commission_percentage').hide();

    $('#can_commission').click(function() {
        if ($(this).is(':checked')) {
            $('#commission_percentage').show();
        } else {
            $('#commission_percentage').hide();
        }
    });

    $('#stock').attr('required', true);

    $('#minimum_stock').attr('required', true);

    $('#type').change(function() {
        if ($('#type').val() === 'sv') {
            $('#stock').attr('disabled', true);
            $('#minimum_stock').attr('disabled', true);
            $('#stock').attr('required', false);
            $('#minimum_stock').attr('required', false);
        } else {
            $('#stock').attr('disabled', false);
            $('#minimum_stock').attr('disabled', false);
            $('#stock').attr('required', true);
            $('#minimum_stock').attr('required', true);
        }
    });
</script>
@endpush
