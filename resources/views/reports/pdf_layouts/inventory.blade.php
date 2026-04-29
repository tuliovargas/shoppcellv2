<html lang="en">
<head>
<meta charset="UTF-8">
<title>Relatório de Estoque</title>

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
            <h3>Relatório de Estoque</h3>
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
        <td><strong>Situação:</strong> {{ $status }}</td>
        <td><strong>Estoque Mínimo:</strong> {{ $minStock }}</td>
        <td><strong>Estoque Atual:</strong> {{ $actualStock }}</td>
    </tr>
  </table>

  <br/>

  <table width="100%">
    <thead style="background-color: lightgray;">
        <tr>
            <th>ID</th>
            <th>Cod. Barras</th>
            <th>Descrição</th>
            <th>Estoque Mínimo</th>
            <th>Estoque Atual</th>
        </tr>
    </thead>
    <tbody>
      @if (!empty($products))
            @foreach ($products as $product)
                <tr>
                    <td style="text-align: center">#{{ $product->id }}</td>
                    <td style="text-align: center">{{ $product->barcode }}</td>
                    <td>{{ $product->name }}</td>
                    <td style="text-align: center">{{ $product->minimum_stock }}</td>
                    <td style="text-align: center">{{ $product->quantity_in_stock }}</td>
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