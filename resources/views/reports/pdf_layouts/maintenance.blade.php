<html lang="en">
<head>
<meta charset="UTF-8">
<title>Relatório de Manutenção</title>

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
            <h3>Relatório de Manutenção</h3>
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
        <td><strong>Técnico:</strong> {{ $tecnician }}</td>
        <td><strong>Situação:</strong> {{ $status }}</td>
    </tr>
    <tr>
        <td><strong>Data Inicial:</strong> {{ $startDate }}</td>
        <td><strong>Data Final:</strong> {{ $endDate }}</td>
    </tr>
  </table>

  <br/>

  <table width="100%">
    <thead style="background-color: lightgray;">
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