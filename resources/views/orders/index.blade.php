@extends('adminlte::page')

@section('title', 'Pedidos')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Pedidos</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="clients" class="table table-bordered table-hover mb-2" style="width:100%">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Vendedor</th>
                                <th>Caixa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@stop

@section('plugins.Datatables', true)

@section('js')
<script src="{{ mix('js/module-clients.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#clients').DataTable({
            "paging": true,
            "lengthChange": false,
            "stripeClasses": ['bg-white', 'bg-light'],
            ajax: {
                url: "{{ route('orders.index') }}",
                data: function(data) {
                    data.client_id = "{{ app('request')->input('client_id') }}"
                }
            },
            columns: [{
                data: 'id',
                name: 'id',
                searchable: true
            },
            {
                data: 'created_at',
                name: 'created_at',
                searchable: true,
                render: function ( data, type, row, meta ) {
                    var data = new Date(data);
                    var dd = String(data.getDate()).padStart(2, "0");
                    var mm = String(data.getMonth() + 1).padStart(2, "0"); //January is 0!
                    var yyyy = data.getFullYear();

                    var hours = data.getHours();
                    var minutes = data.getMinutes();
                    var seconds = data.getSeconds();
                    seconds = (seconds < 10 ? '0' : '') + seconds;

                    data = dd + "/" + mm + "/" + yyyy;
                    var hora = hours + ":" + minutes + ":" + seconds;
                    return '<div style="text-align: center;">' + data + ' ' + hora + '</div>';
                }
            },
            {
                data: 'total',
                name: 'total',
                searchable: true,
                render: function ( data, type, row, meta ) {
                    return '<div style="text-align: right;">R$ ' + Intl.NumberFormat('pt-br', {
                        currency: 'BRL',
                        maximumFractionDigits: 2,
                        minimumFractionDigits: 2
                    }).format(data) + '</div>';
                }
            },
            {
                data: 'seller',
                name: 'seller',
                searchable: true
            },
            {
                data: 'user',
                name: 'user',
                searchable: true
            },
            {
                data: 'status',
                name: 'status',
                searchable: true,
                render: function ( data, type, row, meta ) {
                    let label = "";
                    let classe = '';
                    switch (data) {
                        case "waiting_payment":
                            label = "Aguardando pagamento";
                            classe = 'badge-warning';
                            break;
                        case "waiting_product":
                            label = "Aguardando pagamento";
                            classe = 'badge-warning';
                            break;
                        case "concluded":
                            label = "Concluído";
                            classe = 'badge-success';
                            break;
                        case "canceled":
                            label = "Cancelado";
                            classe = 'badge-danger';
                            break;
                        case "returned":
                            label = "Devolvido";
                            classe = 'badge-success';
                            break;
                    }

                    let html = '<div style="text-align: center;"><span class="right badge status-button h-100 ';  
                    html += classe + '">' + label + '</span></div>';

                    return html;
                }
            }],
            "searching": true,
            search: {
                "regex": true
            },
            "language": {
                search: 'Pesquisar'
            },
            "ordering": true,
            "processing": true,
            "serverSide": true,
            "autoWidth": true,
            "responsive": true,
            "scrollX": true
        });
    });
</script>
@stop
