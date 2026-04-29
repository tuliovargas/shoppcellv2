@extends('adminlte::page')

@section('title', 'Relatório Estoque')

@section('content_header')
    <h1>Relatório Estoque</h1>
@endsection

@section('content')
    @csrf
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                    	<div class="row">
	                        <h3 class="card-title"></h3>
	                    </div>
                        
                        <form method="GET" id="form_report" action="{{ route('reports.inventory') }}">
                            <input value="0" type="hidden" name="is_pdf" id="is_pdf">

                            <div class="row">
                                <div class="col-2"></div>
                                <div class="col-2">
                                <label>Situação</label>
                                </div>
                                <div class="col-2" style="text-align: center;">
                                    <label>Estoque Mínimo entre</label>
                                </div>
                                <div class="col-2" style="text-align: center;">
                                    <label>Estoque Atual entre</label>
                                </div>
                                <div class="col-4"></div>
                            </div>

                        	<div class="row">
                                <div class="col-2"></div>

                                <div class="col-2">
                                    <div class="form-group">
                                        
                                        <select id="active"
                                                name="active"
                                                class="select2"
                                                data-placeholder="Selecione a Situação"
                                                style="width: 100%;">

                                            <option value="-1">Todas as Situações</option>
                                            <option value="1" @if($active == '1') selected @endif>Ativo</option>
                                            <option value="0" @if($active == '0') selected @endif>Inativo</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-1">
                                    <div class="form-group">
                                        <input type="number" class="form-control" name="min_ini" id="min_ini" min="0" value="{{ $min_ini }}">
                                    </div>
                                </div>
                                e
                                <div class="col-1">
                                    <div class="form-group">
                                        <input type="number" class="form-control" name="min_fim" id="min_fim" min="0" value="{{ $min_fim }}">
                                    </div>
                                </div>
                                <div class="col-1">
	                                <div class="form-group">
                                        <input type="number" class="form-control" name="atual_ini" id="atual_ini" min="0" value="{{ $atual_ini }}">
	                                </div>
	                            </div>
                                e
	                            <div class="col-1">
	                                <div class="form-group">
                                        <input type="number" class="form-control" name="atual_fim" id="atual_fim" min="0" value="{{ $atual_fim }}">
	                                </div>
	                            </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-primary" onclick="gerarRelatorio()">Gerar Relatório</button>
                                    <button type="button" class="btn btn-danger" onclick="downloadPdf()">Download PDF</button>
                                </div>
                            </div>
                      	</form>
                    </div>
                </div>
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="users" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cod. Barras</th>
                                    <th>Descrição</th>
                                    <th>Estoque Mínimo</th>
                                    <th>Estoque Atual</th>
                                </tr>
                            </thead>
                            <tbody>
                            	@if (!empty($products))
                                    @foreach ($products as $product)
                                        <tr>
		                                    <td style="text-align: center">#{{ $product->id }}</td>
                                            <td style="text-align: center">{{ $product->barcode }}</td>
		                                    <td>{{ $product->name }}</td>
                                            <td style="text-align: center">{{ $product->minimum_stock }}</td>
                                            <td style="text-align: center">{{ $product->quantity_in_stock }}</td>
	                                    </tr>
                                	@endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.Select2', true)

@section('js')
    <script>
    	$(document).ready(function () {
            $('.select2').select2();
        });

        $(function() {
            $('#users').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
		        "order": [[ 2, "asc" ]]
            });
        });

        function downloadPdf(){
            $('#is_pdf').val('1');
            $('#form_report').prop("target", "_blank");
            $('#form_report').submit();
        }

        function gerarRelatorio(){
            $('#is_pdf').val('0');
            $('#form_report').prop("target", "_self");
            $('#form_report').submit();
        }
    </script>
@stop