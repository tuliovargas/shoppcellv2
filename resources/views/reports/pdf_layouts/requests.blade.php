<html lang="en">
<head>
<meta charset="UTF-8">
<title>Relatório de Pedidos</title>

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
            <h3>Relatório de Pedidos</h3>
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
        <td><strong>Produto:</strong> {{ $product }}</td>
        <td><strong>Data Inicial:</strong> {{ $startDate }}</td>
        <td><strong>Data Final:</strong> {{ $endDate }}</td>
    </tr>
  </table>

  <br/>

  <table width="100%">
    <thead style="background-color: lightgray;">
        <tr>
            <th>Pedido</th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Valor Total</th>
        </tr>
    </thead>
    <tbody>
      @if (!empty($requests))
            @foreach ($requests as $request)
                <tr>
                    <td style="text-align: center">#{{ $request->orderId }}</td>
                    <td style="text-align: center">{{ $request->date }}</td>
                    <td>{{ $request->client }}</td>
                    <td>{{ $request->product_name }}</td>
                    <td style="text-align: center">{{ $request->amount }}</td>
                    <td style="text-align: right">R$ {{ $request->price }}</td>
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