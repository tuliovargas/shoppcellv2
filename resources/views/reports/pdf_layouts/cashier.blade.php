<html lang="en">
<head>
<meta charset="UTF-8">
<title>Relatório de Caixa</title>

<style>
    * {
        font-family: Verdana, Arial, sans-serif;
    }
    table{
        font-size: x-small;
    }
    tfoot tr td{
        font-weight: bold;
        font-size: x-small;
    }
    .gray {
        background-color: lightgray
    }
</style>

</head>
<body>

  <table width="100%">
    <tr>
        <td valign="top"><img src="{{ public_path('/images/logo.png') }}" alt="" width="150"/></td>
        <td align="right">
            <h3>Relatório de Caixa</h3>
            <pre>
                {{ $company_name }}
                {{ $address }}
                {{ $cellphone }}
                {{ $email }}
            </pre>
        </td>
    </tr>

  </table>

  <table width="100%">
    <tr>
        <td><strong>Usuário:</strong> {{ $user }}</td>
        <td><strong>Situação:</strong> {{ $status }}</td>
        <td><strong>Data Inicial:</strong> {{ $startDate }}</td>
        <td><strong>Data Final:</strong> {{ $endDate }}</td>
    </tr>
  </table>

  <br/>

  <table width="100%">
    <thead style="background-color: lightgray;">
        <tr >
            <th>Data Abertura</th>
            <th>Data Fechamento</th>
            <th>Usuário</th>
            <th>Troco</th>
            <th>Situação</th>
        </tr>
    </thead>
    <tbody>
      @if (!empty($cashiers))
            @foreach ($cashiers as $cashier)
                <tr>
                    <td style="text-align: center">{{ $cashier->abertura }}</td>
                    <td style="text-align: center">{{ $cashier->fechamento }}</td>
                    <td>{{ $cashier->usuario }}</td>
                    <td style="text-align: right">R$ {{ $cashier->troco }}</td>
                    <td style="text-align: center">{{ $cashier->status }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>

    <tfoot>
        <!--<tr>
            <td colspan="3"></td>
            <td align="right">Subtotal $</td>
            <td align="right">1635.00</td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td align="right">Tax $</td>
            <td align="right">294.3</td>
        </tr> -->
    </tfoot>
  </table>

</body>
</html>