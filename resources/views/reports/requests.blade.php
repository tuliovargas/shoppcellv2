@extends('adminlte::page')

@section('title', 'Relatório Pedidos')

@section('content_header')
    <h1>Relatório Pedidos</h1>
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
                        
                        <form method="GET" id="form_report" action="{{ route('reports.requests') }}">
                            <input value="0" type="hidden" name="is_pdf" id="is_pdf">

                        	<div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select id="client"
                                                name="client"
                                                class="select2"
                                                data-placeholder="Selecione o Cliente"
                                                style="width: 100%;">

                                            <option value="0">Todos os Clientes</option>

                                            @foreach ($clients as $client)
                                                <option {{ $client->id == $clientId ? 'selected' : '' }} value="{{ $client->id }}">
                                                    {{ $client->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Produto</label>
                                        <select id="product"
                                                name="product"
                                                class="select2"
                                                data-placeholder="Selecione o Produto"
                                                style="width: 100%;">

                                            <option value="0">Todos os Produtos</option>

                                            @foreach ($products as $product)
                                                <option {{ $product->id == $productId ? 'selected' : '' }} value="{{ $product->id }}">
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-2">
	                                <div class="form-group">
	                                    <label>Data Inicial</label>
	                                    <div class="input-group">
	                                        <div class="input-group-prepend">
	                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
	                                        </div>
	                                        <input value="{{ $startDate }}" type="date" class="form-control" name="start_date" id="start_date">
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="col-2">
	                                <div class="form-group">
	                                    <label>Data Final</label>
	                                    <div class="input-group">
	                                        <div class="input-group-prepend">
	                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
	                                        </div>
	                                        <input value="{{ $endDate }}" type="date" class="form-control" name="end_date" id="end_date">
	                                    </div>
	                                </div>
	                            </div>

                                <div class="col-12" style="text-align: center;">
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
                                    <th>Pedido</th>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalAmount = $totalPrice = 0;
                                @endphp
                            	@if (!empty($requests))
                                    @foreach ($requests as $request)
                                    @php
                                        $totalAmount += $request->amount;
                                        $totalPrice += \App\Helpers::unformatCurrency($request->price);
                                    @endphp
                                        <tr>
		                                    <td style="text-align: center">#{{ $request->orderId }}</td>
		                                    <td style="text-align: center">{{ $request->date }}</td>
                                            <td>{{ $request->client }}</td>
                                            <td>{{ $request->product_name }}</td>
                                            <td style="text-align: center">{{ $request->amount }}</td>
		                                    <td style="text-align: right">R$ {{ $request->price }}</td>
	                                    </tr>
                                	@endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr style="font-weight: bold">
                                    <td colspan="4" style="text-align: right">Total:</td>
                                    <td style="text-align: center">{{ $totalAmount }}</td>
                                    <td style="text-align: right">R$ {{ number_format($totalPrice, 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
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
		        "order": [[ 1, "desc" ]]
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