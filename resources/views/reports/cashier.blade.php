@extends('adminlte::page')

@section('title', 'Relatório Caixa')

@section('content_header')
    <h1>Relatório Caixa</h1>
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
                        
                        <form method="GET" id="form_report" action="{{ route('reports.cashier') }}">
                            <input value="0" type="hidden" name="is_pdf" id="is_pdf">

                        	<div class="row">
                                <div class="col-1"></div>

                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Usuário</label>
                                        <select id="user"
                                                name="user"
                                                class="select2"
                                                data-placeholder="Selecione o Usuário"
                                                style="width: 100%;">

                                            <option value="0">Todos os Usuários</option>

                                            @foreach ($users as $user)
                                                <option {{ $user->id == $userId ? 'selected' : '' }} value="{{ $user->id }}">
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Situação</label>
                                        <select id="status"
                                                name="status"
                                                class="select2"
                                                data-placeholder="Selecione a Situação"
                                                style="width: 100%;">

                                            <option value="0">Todas as Situações</option>
                                            <option value="opened" @if($status == 'opened') selected @endif>Aberto</option>
                                            <option value="closed" @if($status == 'closed') selected @endif>Fechado</option>
                                            
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
                                    <th>Data Abertura</th>
                                    <th>Data Fechamento</th>
                                    <th>Usuário</th>
                                    <th>Troco</th>
                                    <th>Situação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalTroco = 0;
                                @endphp
                            	@if (!empty($cashiers))
                                    @foreach ($cashiers as $cashier)
                                    @php
                                        $totalTroco += \App\Helpers::unformatCurrency($cashier->troco);
                                    @endphp
                                        <tr>
		                                    <td style="text-align: center"><a href="javascript:void(0)" onclick="showCashierOrders({{ $cashier->id }})">{{ $cashier->abertura }}</a></td>
		                                    <td style="text-align: center">{{ $cashier->fechamento }}</td>
                                            <td>{{ $cashier->usuario }}</td>
		                                    <td style="text-align: right">R$ {{ $cashier->troco }}</td>
                                            <td style="text-align: center">{{ $cashier->status }}</td>
	                                    </tr>
                                	@endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr style="font-weight: bold">
                                    <td colspan="3" style="text-align: right">Total:</td>
                                    <td style="text-align: right">R$ {{number_format($totalTroco, 2, ',', '.')}}</td>
                                    <td></td>
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
        <div class="modal-dialog" style="max-width: 1000px;">

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
		        "order": [[ 0, "desc" ]]
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

        function showCashierOrders(id){
            $.ajax({
		        type: "GET",
		        url: "/cashierInfo?id=" + id,
                dataType: "json",
		        success: function (response) {
                    let html = '<table class="table table-bordered table-hover">' +
                                    '<thead>' + 
                                        '<tr>' +
                                            '<th>Pedido</th>' +
                                            '<th>Cliente</th>' +
                                            '<th>Desconto</th>' +
                                            '<th>Total</th>' +
                                            '<th>Situação</th>' +
                                            '<th>Meio Pgto</th>' +
                                            '<th>Vendedor</th>' +
                                        '</tr>' +
                                    '</thead>' + 
                                    '<tbody>';

                    let totalVendas = 0;
                    let totalDescontos = 0;

                    response.orders.forEach(function(element, index){
                        html += '<tr>';

                        html += '<td>#' + element.id + '</td>';
                        html += '<td>' + element.client.full_name + '</td>';
                        html += '<td style="text-align: right">R$ ' + element.discount + '</td>';
                        html += '<td style="text-align: right">R$ ' + element.total + '</td>';
                        html += '<td style="text-align: center">' + getStatus(element.status) + '</td>';
                        html += '<td>' + (element.payments.length > 0 ? element.payments[0].payment_method.name : '') + '</td>';
                        html += '<td>' + element.seller.name + '</td>';

                        
                        html += '</tr>';

                        totalVendas += parseFloat(element.total);
                        totalDescontos += parseFloat(element.discount);
                        
                    });

                    html += '<tfoot>' + 
                                '<tr style="font-weight: bold">' + 
                                    '<td colspan="2" style="text-align: right">Total:</td>' + 
                                    '<td style="text-align: right">R$ ' + totalDescontos.toFixed(2) + '</td>' + 
                                    '<td style="text-align: right">R$ ' + totalVendas.toFixed(2) + '</td>' + 
                                    '<td></td>' + 
                                    '<td></td>' + 
                                    '<td></td>' +
                                '</tr>' + 
                            '</tfoot>' + 
                            
                            '</tbody>' +
                        '</table>';

                    $('#modal-header').html('Pedidos do Caixa #' + id);
                    $('#modal-body').html(html);
                    $('#myModal').modal('show');
		        },
		        error: function (error) {
		           console.log(error);
		        }
		    });

            function getStatus(status){
                
                let allStatus = {
                    'is_request': 'Orçamento', 
                    'waiting_approval': 'Aguardando Aprovação do Cliente', 
                    'approved': 'Aprovado pelo Cliente',
                    'waiting_product': 'Aguardando Produto',
                    'maintenance': 'Em Manutenção', 
                    'waiting_payment': 'Aguardando Pagamento', 
                    'concluded': 'Concluído', 
                    'canceled': 'Cancelado', 
                    'returned': 'Devolvido', 
                    'waiting_maintenance': 'Aguardando Manutenção', 
                    'partially_returned': 'Parcialmente Devolvido' 
                };

                return allStatus[status];
                
            }
        }
    </script>
@stop
