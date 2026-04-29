@extends('adminlte::page')

@section('title', 'Relatório Comissão')

@section('content_header')
    <h1>Relatório Comissão</h1>
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
                        
                        <form method="GET" action="{{ route('reports.commission') }}">
                        	<div class="row">
								<div class="col-1"></div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Usuário</label>
                                        <select id="user"
                                                name="user"
                                                class="select2"
                                                data-placeholder="Selecione o Usuário"
                                                style="width: 100%;">

                                            <option value="">Selecione o Usuário</option>

                                            @foreach ($users as $user)
                                                <option {{ $user->id == $userId ? 'selected' : '' }} value="{{ $user->id }}">
                                                    {{ $user->name }}
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

                                <div class="col-3" style="padding-top: 2em;">
                                	<button type="submit" class="btn btn-primary">Gerar Relatório</button>
                                	@if(!empty($cashier))
                                		<button type="button" class="btn btn-danger" onclick="pagarComissao()">Pagar Comissão</button>
                                	@endif
                                </div>
                            </div>
                      	</form>
                      	<div class="alert alert-danger" id="alert-empty">
  							Selecione pelo menos um produto para pagar
						</div>
						<div class="alert alert-danger" id="alert-error">
  							Erro ao processar pagamento
						</div>
						<div class="alert alert-success" id="alert-payd">
  							Comissão paga com sucesso
						</div>
                    </div>
                </div>
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                    	<a href="javaScript:void(0)" onclick="selecionarTodos()">Selecionar Todos</a>

                        <table id="users" class="table table-bordered table-hover">
                            <thead>
                                <tr >
                                	<th>#</th>
                                    <th>Pedido</th>
                                    <th>Data</th>
									<th>Status</th>
                                    <th>Nome do Produto</th>
                                    <th>Valor de Venda</th>
                                    <th>Perc. Comissão</th>
                                    <th>Valor Comissão</th>
                                </tr>
                            </thead>
                            <tbody>
								@php
									$totalSale = $totalCommission = 0;
								@endphp
                            	@if (!empty($cashier))
                                    @foreach ($cashier as $product)
									@php
										$totalSale += \App\Helpers::unformatCurrency($product->price);
										$totalCommission += \App\Helpers::unformatCurrency($product->commission);
									@endphp
                                        <tr>
                                        	<td style="text-align: center">
                                        		<div class="form-check">
			                                        <input type="checkbox"
			                                               class="form-check-input"
			                                               data-id="{{ $product->id }}"
			                                               @if($product->commission_payed) disabled @endif>
			                                    </div>
                                        	</td>
		                                    <td>{{ $product->orderId }}</td>
		                                    <td style="text-align: center">{{ $product->date }}</td>
											<td style="text-align: center">
												@if ($product->commission_payed == 1)
													<span class="badge badge-success">
														Pago
													</span>
												@else
													<span class="badge badge-warning">
														Pendente
													</span>
												@endif
											</td>
		                                    <td>{{ $product->name }}</td>
		                                    <td style="text-align: right">R$ {{ $product->price }}</td>
		                                    <td style="text-align: right">{{ $product->percentage }} %</td>
		                                    <td style="text-align: right">R$ {{ $product->commission }}</td>
	                                    </tr>
                                	@endforeach
                                @endif
                            </tbody>
							<tfoot>
								<tr style="font-weight: bold">
									<td colspan="5" style="text-align: right">Total:</td>
									<td style="text-align: right">R$ {{number_format($totalSale, 2, ',', '.')}}</td>
									<td style="text-align: center">---</td>
									<td  style="text-align: right">R$ {{number_format($totalCommission, 2, ',', '.')}}</td>
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
    	$('#alert-empty').hide();
    	$('#alert-error').hide();
    	$('#alert-payd').hide();
    	var allSelected = false;

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
                columnDefs: [
		            { orderable: false, targets: [0] },
		        ],
		        "order": [[ 2, "desc" ]]
            });
        });

        function selecionarTodos(){
        	$('#alert-empty').hide();
        	allSelected = !allSelected;

        	$('.form-check-input').each(function( index ) {
        		if($(this).is(':disabled') == false){
        			if(allSelected){
	        			$( this ).prop( "checked", true );
	        		} else{
	        			$( this ).prop( "checked", false );
	        		}
        		}
			});
        }

        function pagarComissao(){
        	var products = [];

        	$('.form-check-input').each(function( index ) {
        		if( $(this).is(":checked")){
        			products.push($(this).data('id'));
        		}
			});

			if(products.length == 0){
				$('#alert-empty').show();
				return;
			}

			$('#alert-empty').hide();

		    var data = {
		        _token: $("input[name=_token]").val(),
		        products: products,
		        user: $('#user').val(),
		        start_date: $('#start_date').val(),
		        end_date: $('#end_date').val()
		    };

		    $.ajax({
		        type: "POST",
		        url: "/commission",
		        //dataType: "json",
		        data: data,
		        timeout: 0, // sets timeout to infinite seconds
                xhrFields: {
                    responseType: 'blob'
                },
		        success: function (response) {
                    var blob = new Blob([response]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = "Relatório de Comissão.pdf";
                    link.click();

                    $('#alert-payd').show();

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);


		        	/*if(data.processed == true){
		        		$('#alert-payd').show();

		        		setTimeout(function () {
		                    window.location.reload();
		                }, 2000);
		        	} else{
		        		$('#alert-error').show();
		        	}*/
		        },
		        error: function (error) {
		           console.log(error);
		           $('#alert-error').show();
		        }
		    });
        }

    </script>
@stop