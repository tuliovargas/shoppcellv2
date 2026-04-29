@extends('adminlte::page')

@section('title', 'Relatório Vendas')

@section('content_header')
    <h1>Relatório Vendas</h1>
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
                        
                        <form id="form_report" method="GET" action="{{ route('reports.sales') }}">
                            <input value="0" type="hidden" name="is_pdf" id="is_pdf">

                        	<div class="row">
                                <div class="col-3">
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

                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Vendedor</label>
                                        <select id="seller"
                                                name="seller"
                                                class="select2"
                                                data-placeholder="Selecione o Vendedor"
                                                style="width: 100%;">

                                            <option value="0">Todos os Vendedores</option>

                                            @foreach ($users as $user)
                                                <option {{ $user->id == $sellerId ? 'selected' : '' }} value="{{ $user->id }}">
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Situação</label>
                                        <select id="status"
                                                name="status"
                                                class="select2"
                                                data-placeholder="Selecione a Situação"
                                                style="width: 100%;">

                                            <option value="0">Todas as Situações</option>
                                            <option value="waiting_product" @if($status == 'waiting_product') selected @endif>Aguardando Produto</option>
                                            <option value="waiting_payment" @if($status == 'waiting_payment') selected @endif>Aguardando Pagamento</option>
                                            <option value="concluded" @if($status == 'concluded') selected @endif>Concluído</option>
                                            <option value="canceled" @if($status == 'canceled') selected @endif>Cancelado</option>
                                            <option value="partially_returned" @if($status == 'partially_returned') selected @endif>Parcialmente Devolvido</option>
                                            <option value="returned" @if($status == 'returned') selected @endif>Devolvido</option>
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
                                <tr >
                                    <th>Pedido</th>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th>Desconto</th>
                                    <th>Total</th>
                                    <th>Comissão</th>
                                    <th>Situação</th>
                                    <th>Meio Pgto.</th>
                                    <th>Vendedor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $totalDiscount = $total = $totalCommission = 0;
                                @endphp
                            	@if (!empty($orders))
                                    @foreach ($orders as $order)
                                        @php
                                        $totalDiscount += \App\Helpers::unformatCurrency($order->discount);
                                        $total += \App\Helpers::unformatCurrency($order->price);
                                        $totalCommission += \App\Helpers::unformatCurrency($order->commission);
                                        @endphp
                                        <tr>
		                                    <td style="text-align: center"><a href="javascript:void(0)" onclick="showOrderProducts({{ $order->orderId }})">#{{ $order->orderId }}</a></td>
		                                    <td style="text-align: center">{{ $order->date }}</td>
                                            <td>{{ $order->client }}</td>
                                            <td style="text-align: right">R$ {{ $order->discount }}</td>
		                                    <td style="text-align: right">R$ {{ $order->price }}</td>
                                            <td style="text-align: right"><a href="javascript:void(0)" onclick="showCommisionDetails({{ $order->orderId }})">R$ {{ $order->commission }}</a></td>
                                            <td style="text-align: center">{{ $order->status }}</td>
                                            <td>{{ $order->pagamento }}</td>
		                                    <td>{{ $order->seller }}</td>
	                                    </tr>
                                	@endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr style="font-weight: bold">
                                    <td colspan="3" style="text-align: right">Total:</td>
                                    <td style="text-align: right">R$ {{ number_format($totalDiscount, 2, ',', '.')}}</td>
                                    <td style="text-align: right">R$ {{number_format($total, 2, ',', '.')}}</td>
                                    <td style="text-align: right">R$ {{ number_format($totalCommission, 2, ',', '.')}}</td>
                                    <td colspan="3"></td>
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


    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
            <div class="modal-header">
                
                <h4 class="modal-title" id="modal-header">Modal Header</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
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

        function showOrderProducts(orderId){
            $.ajax({
		        type: "GET",
		        url: "/orders/" + orderId,
                dataType: "json",
		        success: function (response) {
                    let html = '';

                    response.products.forEach(function(element, index){
                        html += '<p>' + element.product.name + ' - R$ ' + element.price + '</p>';

                        element.by_products.forEach(function(element, index){
                            html += '<p>' + element.product.name + ' - R$ ' + element.price +  '</p>';
                        });
                    });

                    $('#modal-header').html('Produtos do Pedido #' + orderId);
                    $('#modal-body').html(html);
                    $('#myModal').modal('show');
		        },
		        error: function (error) {
		           console.log(error);
		        }
		    });
        }

        function showCommisionDetails(orderId){
            $.ajax({
		        type: "GET",
		        url: "/orders/" + orderId,
                dataType: "json",
		        success: function (response) {
                    let html = '';

                    response.products.forEach(function(orderProduct, index){
                        if(orderProduct.commission > 0){
                            html += '<p>' + response.seller.name + ': ' + orderProduct.product.name + ' - R$ ' + orderProduct.commission + '</p>';
                        }

                        if(orderProduct.technician_commission > 0){
                            html += '<p>' + orderProduct.maintenance.tecnician.name + ': ' + orderProduct.product.name + ' - R$ ' + orderProduct.technician_commission +  '</p>';
                        }
                    
                        orderProduct.by_products.forEach(function(by_product, index){
                            if(by_product.commission > 0){
                                html += '<p>' + response.seller.name + ': ' + by_product.product.name + ' - R$ ' + by_product.commission +  '</p>';
                            }

                            if(by_product.technician_commission > 0){
                                html += '<p>' + orderProduct.maintenance.tecnician.name + ': ' + by_product.product.name + ' - R$ ' + by_product.technician_commission +  '</p>';
                            }
                        });
                    });

                    if(html.length == 0){
                        return;
                    }

                    $('#modal-header').html('Comissões do Pedido #' + orderId);
                    $('#modal-body').html(html);
                    $('#myModal').modal('show');
		        },
		        error: function (error) {
		           console.log(error);
		        }
		    });
        }
    </script>
@stop