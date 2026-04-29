<html lang="en">
<head>
<meta charset="UTF-8">
<title>Relatório de Comissão</title>

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
            <h3>Relatório de Comissão</h3>
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
        <td><strong>Colaborador:</strong> {{ $colaborador }}</td>
        <td><strong>Data Inicial:</strong> {{ $startDate }}</td>
        <td><strong>Data Final:</strong> {{ $endDate }}</td>
    </tr>

  </table>

  <br/>

  <table width="100%">
    <thead style="background-color: lightgray;">
        <tr >
            <th>Pedido</th>
            <th>Data</th>
            <th>Nome</th>
            <th>Valor de Venda</th>
            <th>Perc. Comissão</th>
            <th>Valor Comissão</th>
        </tr>
    </thead>
    <tbody>
      @if (!empty($cashier))
            @foreach ($cashier as $product)
                <tr>
                    <td style="text-align: center">{{ $product->orderId }}</td>
                    <td style="text-align: center">{{ $product->date }}</td>
                    <td>{{ $product->name }}</td>
                    <td style="text-align: right">R$ {{ $product->price }}</td>
                    <td style="text-align: right">{{ $product->percentage }} %</td>
                    <td style="text-align: right">R$ {{ $product->commission }}</td>
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
            <td colspan="4"></td>
            <td align="right">Total R$</td>
            <td align="right" class="gray">R$ {{ $total }}</td>
        </tr>
    </tfoot>
  </table>

</body>
</html>