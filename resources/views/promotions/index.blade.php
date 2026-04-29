@extends('adminlte::page')

@section('title', 'Promoções')

@section('content_header')
    <h1>Promoções</h1>
@stop

@section('content')
    <div class="container-fluid">
    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <a href="javaScript:void(0)" class="btn btn-primary btn-block mb-3" onclick="enviar()">Enviar</a>
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Opções</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <ul class="nav nav-pills flex-column">
                <li class="nav-item active">
                  <a href="/promotions/week-buyers" class="nav-link">
                    <i class="fas fa-inbox"></i> Clientes que fizeram compras @if($period_buyers == 'month') esse mês @else essa semana @endif
                    <span class="badge bg-primary float-right">{{ $countWeekBuyers }}</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/promotions/birthdays" class="nav-link">
                    <i class="far fa-envelope"></i> Aniversariantes @if($period_birthdays == 'day') do dia @elseif($period_birthdays == 'week') da semana @else do mês @endif
                    <span class="badge bg-primary float-right">{{ $countBirthdays }}</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/promotions/detached" class="nav-link">
                    <i class="far fa-file-alt"></i> Promoção avulsa
                    <span class="badge bg-primary float-right">{{ $countWeekDetached }}</span>
                  </a>
                </li>
              </ul>
            </div>
            <!-- /.card-body -->
          </div>
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <a href="javaScript:void(0)" onclick="selecionarTodos()">Selecionar Todos</a>
                        </div>

                        @if($type == 'birthdays')
                          <div class="col-md-8" style="text-align: right;">
                          </div>

                          <div class="col-md-2" style="float: right;">
                            <select id="period"
                                    name="period_birthdays"
                                    class="select2"
                                    style="width: 100%;"
                                    onchange="periodChange('period_birthdays')">

                                <option value="day" @if($period_birthdays == 'day') selected @endif>Dia</option>
                                <option value="week" @if($period_birthdays == 'week') selected @endif>Semana</option>
                                <option value="month" @if($period_birthdays == 'month') selected @endif>Mês</option>
                            </select>
                          </div>
                          @elseif($type == 'week-buyers')
                            <div class="col-md-8" style="text-align: right;">
                            </div>

                            <div class="col-md-2" style="float: right;">
                              <select id="period"
                                      name="period_buyers"
                                      class="select2"
                                      style="width: 100%;"
                                      onchange="periodChange('period_buyers')">

                                  <option value="week" @if($period_buyers == 'week') selected @endif>Semana</option>
                                  <option value="month" @if($period_buyers == 'month') selected @endif>Mês</option>
                              </select>
                            </div>
                          @endif
                    </div>

                    <table id="users" class="table table-bordered table-hover">
                        <thead>
                            <tr >
                                <th>#</th>
                                <th>Nome</th>
                                <th>Celular</th>
                                <th>Mensagem</th>
                            </tr>
                        </thead>
                        <tbody> 
                            
                            @if (!empty($clients))
                                @foreach ($clients as $key=>$client)
                                    @php
                                        $name = strtolower(explode(' ', $client->full_name)[0]);
                                        $name = ucfirst($name);
                                        $msg = str_replace("{user_firstname}", $name, $message);
                                        $msg = str_replace("{produto_firstname}", $client->product_name, $msg);

                                        $phone = \App\Services\Utilities\Util::removeMask($client->getRawOriginal('cellphone'));
                                        if(!str_starts_with($phone, '55')){
                                          $phone = '55' . $phone;
                                        }

                                        $url = 'https://api.whatsapp.com/send/?phone=' . $phone . '&text=' . urlencode($msg) . '&app_absent=0';
                                    @endphp
                                    <tr>
                                        <td style="text-align: center">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                        class="form-check-input"
                                                        data-id="{{ $key }}"
                                                        data-url="{{ $url }}">
                                            </div>
                                        </td>
                                        <td>{{ ucwords(strtolower($client->full_name)) }}</td>
                                        <td style="text-align: center">{{ \App\Services\Utilities\Util::formatarTelefone($phone) }}</td>
                                        <td><div id="message-{{ $key }}">{{ $msg }}</div></td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)

@section('js')
    <script>
        var allSelected = false;

        $(document).ready(function () {
            $('.select2').select2();
        });

        $(function() {
            $('#users').DataTable({
                "paging": true,
                "pageLength": 100,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                columnDefs: [
		            { orderable: false, targets: [0] },
		        ],
		        "order": [[ 1, "asc" ]]
            });
        });

        function selecionarTodos(){
        	$('#alert-empty').hide();
              allSelected = !allSelected;

              $('.form-check-input').each(function( index ) {
                    if(allSelected){
                        $( this ).prop( "checked", true );
                    } else{
                        $( this ).prop( "checked", false );
                    }
          });
        }

        function enviar(){
        	$('.form-check-input').each(function( index ) {
        		if( $(this).is(":checked") || allSelected){
                    let url = $(this).data('url');
                    window.open(url, '_blank');
        		}
			    });
        }

        function periodChange(parameter){
            window.location.href = '//' + location.host + location.pathname + "?" + parameter + "=" + $('#period').val() ;
        }
    </script>
@stop
