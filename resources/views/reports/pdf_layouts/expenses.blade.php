<html lang="en">
<head>
<meta charset="UTF-8">
<title>Relatório de Despesas</title>

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
            <h3>Relatório de Despesas</h3>
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
        <td><strong>Tipo Pagamento:</strong> {{ $tipo }}</td>
        <td><strong>Método Pagamento:</strong> {{ $metodo }}</td>
        <td><strong>Dt Lanc. Inicial:</strong> {{ $startLancDate }}</td>
    </tr>
    <tr>
        <td><strong>Dt Lanc. Final:</strong> {{ $endLancDate }}</td>
        <td><strong>Dt Pgto Inicial:</strong> {{ $startPgtoDate }}</td>
        <td><strong>Dt Pgto Final:</strong> {{ $endPgtoDate }}</td>
    </tr>

  </table>

  <br/>

  <table width="100%">
    <thead style="background-color: lightgray;">
        <tr>
            <th>ID</th>
            <th>Data Lanc.</th>
            <th>Data Pgto</th>
            <th>Descrição</th>
            <th>Tipo</th>
            <th>Método Pgto</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        @if (!empty($expenses))
            @foreach ($expenses as $expense)
                <tr>
                    <td style="text-align: center">#{{ $expense->id }}</td>
                    <td style="text-align: center">{{ $expense->date }}</td>
                    <td style="text-align: center">{{ $expense->payment_date }}</td>
                    <td>{{ $expense->name }}</td>
                    <td style="text-align: center">{{ $expense->expenseType->name }}</td>
                    <td style="text-align: center">{{ $expense->paymentMethod->name }}</td>
                    <td style="text-align: right">R$ {{ \App\Services\Utilities\Util::doubleToString($expense->value) }}</td>
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
            <td colspan="5"></td>
            <td align="right">Total R$</td>
            <td align="right" class="gray">R$ {{ $total }}</td>
        </tr>
    </tfoot>
  </table>

</body>
</html>