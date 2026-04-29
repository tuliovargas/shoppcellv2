<html lang="en">
<head>
<meta charset="UTF-8">
<title>Relatório de Vendas</title>

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
            <h3>Relatório de Vendas</h3>
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
        <td><strong>Cliente:</strong> {{ $client }}</td>
        <td><strong>Vendedor:</strong> {{ $seller }}</td>
        <td><strong>Situação:</strong> {{ $status }}</td>
    </tr>
    <tr>
        <td><strong>Data Inicial:</strong> {{ $startDate }}</td>
        <td><strong>Data Final:</strong> {{ $endDate }}</td>
    </tr>

  </table>

  <br/>

  <table width="100%" style="font-size: 10px">
    <thead style="background-color: lightgray;">
        <tr>
            <th>Pedido</th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Desconto</th>
            <th>Comissão</th>
            <th>Situação</th>
            <th>Meio Pgto.</th>
            <th>Vendedor</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
      @if (!empty($orders))
            @foreach ($orders as $order)
                <tr>
                    <td style="text-align: center">#{{ $order->orderId }}</td>
                    <td style="text-align: center">{{ $order->date }}</td>
                    <td>{{ $order->client }}</td>
                    <td style="text-align: right">R$ {{ $order->discount }}</td>
                    <td style="text-align: right">R$ {{ $order->commission }}</td>
                    <td style="text-align: center">{{ $order->status }}</td>
                    <td>{{ $order->pagamento }}</td>
                    <td>{{ $order->seller }}</td>
                    <td style="text-align: right;">R$ {{ $order->price }}</td>
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
        <tr>
            <td colspan="7"></td>
            <td align="right" style="font-size: 10px">Total R$</td>
            <td align="right" class="gray" style="font-size: 10px">R$ {{ $total }}</td>
        </tr>
    </tfoot>
  </table>

</body>
</html>