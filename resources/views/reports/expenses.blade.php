@extends('adminlte::page')

@section('title', 'Relatório Despesas')

@section('content_header')
    <h1>Relatório Despesas</h1>
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
                        
                        <form method="GET" id="form_report" action="{{ route('reports.expenses') }}">
                            <input value="0" type="hidden" name="is_pdf" id="is_pdf">

                        	<div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Tipo Pagamento</label>
                                        <select id="tipo"
                                                name="tipo"
                                                class="select2"
                                                data-placeholder="Selecione o Cliente"
                                                style="width: 100%;">

                                            <option value="0">Todos os Tipos</option>

                                            @foreach ($tipos as $tipo)
                                                <option {{ $tipo->id == $tipoId ? 'selected' : '' }} value="{{ $tipo->id }}">
                                                    {{ $tipo->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Método Pagamento</label>
                                        <select id="metodo"
                                                name="metodo"
                                                class="select2"
                                                data-placeholder="Selecione o Produto"
                                                style="width: 100%;">

                                            <option value="0">Todos os Métodos</option>

                                            @foreach ($metodos as $metodo)
                                                <option {{ $metodo->id == $metodoId ? 'selected' : '' }} value="{{ $metodo->id }}">
                                                    {{ $metodo->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-2">
	                                <div class="form-group">
	                                    <label>Data Lançamento Inicial</label>
	                                    <div class="input-group">
	                                        <div class="input-group-prepend">
	                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
	                                        </div>
	                                        <input value="{{ $startLancDate }}" type="date" class="form-control" name="start_lanc_date" id="start_date">
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="col-2">
	                                <div class="form-group">
	                                    <label>Data Lançamento Final</label>
	                                    <div class="input-group">
	                                        <div class="input-group-prepend">
	                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
	                                        </div>
	                                        <input value="{{ $endLancDate }}" type="date" class="form-control" name="end_lanc_date" id="end_date">
	                                    </div>
	                                </div>
	                            </div>

                                <div class="col-2">
	                                <div class="form-group">
	                                    <label>Data Pgto Inicial</label>
	                                    <div class="input-group">
	                                        <div class="input-group-prepend">
	                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
	                                        </div>
	                                        <input value="{{ $startPgtoDate }}" type="date" class="form-control" name="start_pgto_date" id="start_date">
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="col-2">
	                                <div class="form-group">
	                                    <label>Data Pgto Final</label>
	                                    <div class="input-group">
	                                        <div class="input-group-prepend">
	                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
	                                        </div>
	                                        <input value="{{ $endPgtoDate }}" type="date" class="form-control" name="end_pgto_date" id="end_date">
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
                                    <th>ID</th>
                                    <th>Data Lançamento</th>
                                    <th>Data Pagamento</th>
                                    <th>Descrição</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Método de Pagamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalValue = 0;
                                @endphp
                            	@if (!empty($expenses))
                                    @foreach ($expenses as $expense)
                                    @php
                                        $totalValue += \App\Helpers::unformatCurrency($expense->value);
                                    @endphp
                                        <tr>
		                                    <td style="text-align: center">#{{ $expense->id }}</td>
		                                    <td style="text-align: center">{{ $expense->date }}</td>
                                            <td style="text-align: center">{{ $expense->payment_date }}</td>
                                            <td>{{ $expense->name }}</td>
                                            <td style="text-align: center">{{ $expense->expenseType->name }}</td>
		                                    <td style="text-align: right">R$ {{ $expense->value }}</td>
                                            <td style="text-align: center">{{ $expense->paymentMethod->name }}</td>
	                                    </tr>
                                	@endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr style="font-weight: bold">
                                    <td style="text-align: right" colspan="5">Total:</td>
                                    <td style="text-align: right">R$ {{ number_format($totalValue, 2, ',', '.') }}</td>
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
		        "order": [[ 2, "desc" ]]
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