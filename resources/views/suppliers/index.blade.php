@extends('adminlte::page')

@section('title', 'Fornecedores')

@section('content_header')
<h1>Fornecedores</h1>
@stop

@section('content')
<div class="container-fluid">
  <div class="row">
    @if (session('success'))
    <div class="col-12">
      <div class="alert alert-success">
        Fornecedor removido com sucesso.
      </div>
    </div>
    @endif

    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Lista de Fornecedores</h3>
          <div class="card-tools">
            <a href="{{route('suppliers.create')}}">Cadastrar Novo Fornecedor</a>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="suppliers" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Imagem</th>
                <th>Nome</th>
                <th>CNPJ</th>
                <th>Responsável</th>
                <th>Telefone</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($suppliers as $supplier)
              <tr>
                <td>{{$supplier->id}}</td>
                <td>
                  @if ($supplier->photo_url)
                  <img width="48" height="48" src="{{asset('storage/'.$supplier->photo_url)}}">
                  @else
                  <img width="48" height="48" src="{{ asset('images/default-image.png') }}">
                  @endif
                </td>
                <td>{{$supplier->name}}</td>
                <td>{{\App\Services\Utilities\Util::formatarCpfCnpj($supplier->cnpj)}}</td>
                <td>{{$supplier->responsible_person}}</td>
                <td>{{\App\Services\Utilities\Util::formatarTelefone($supplier->cellphone)}}</td>
                <td>
                  <a href="{{route('suppliers.edit', ['supplier' => $supplier->id])}}">
                    <i class="mx-2 fas fa-pen"></i>
                  </a>
                  <a href="{{route('suppliers.delete', ['supplier' => $supplier->id])}}">
                    <i class="mx-2 fas fa-trash"></i>
                  </a>
                </td>
              </tr>
              @endforeach
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
<script>
  $(function () {
        $('#suppliers').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "language": {
            search: 'Pesquisar'
        },
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        });
    });
</script>
@stop