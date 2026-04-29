@extends('adminlte::page')

@section('title', 'Relatório Manutenção')

@section('content_header')
    <h1>Relatório Manutenção</h1>
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
                        
                        <form method="GET" id="form_report" action="{{ route('reports.maintenance') }}">
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
                                        <label>Técnico</label>
                                        <select id="tecnician"
                                                name="tecnician"
                                                class="select2"
                                                data-placeholder="Selecione o Técnico"
                                                style="width: 100%;">

                                            <option value="0">Todos os Técnicos</option>

                                            @foreach ($tecnicians as $tecnician)
                                                <option {{ $tecnician->id == $tecnicianId ? 'selected' : '' }} value="{{ $tecnician->id }}">
                                                    {{ $tecnician->name }}
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
                                            <option value="waiting_approval" @if($status == 'waiting_approval') selected @endif>Aguardando Aprovação</option>
                                            <option value="approved" @if($status == 'approved') selected @endif>Aprovado pelo Cliente</option>
                                            <option value="waiting_stock" @if($status == 'waiting_stock') selected @endif>Aguardando Peça</option>
                                            <option value="maintenance" @if($status == 'maintenance') selected @endif>Em Manutenção</option>
                                            <option value="no_maintenance" @if($status == 'no_maintenance') selected @endif>Sem Concerto</option>
                                            <option value="fixed" @if($status == 'fixed') selected @endif>Finalizado/Consertado</option>
                                            <option value="finished" @if($status == 'finished') selected @endif>Enviado para o Caixa</option>
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
                                    <th>Manutenção</th>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th>Produto</th>
                                    <th>Situação</th>
                                    <th>Técnico</th>
                                </tr>
                            </thead>
                            <tbody>
                            	@if (!empty($orders))
                                    @foreach ($orders as $order)
                                        <tr>
		                                    <td style="text-align: center">#{{ $order->orderId }}</td>
		                                    <td style="text-align: center">{{ $order->date }}</td>
                                            <td>{{ $order->client }}</td>
		                                    <td>{{ $order->products }}</td>
                                            <td style="text-align: center">{{ $order->status }}</td>
		                                    <td>{{ $order->tecnician }}</td>
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
