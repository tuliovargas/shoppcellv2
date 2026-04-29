@extends('print.layout')

@section('header')
<table class="border-t">
    <tr>
        <td>
            <p class="left">
                {{ \Carbon\Carbon::parse($order->canceled_at)->format('d/m/Y H:i')}}
            </p>
        </td>
        <td>
            <p class="right" style="font-size: 12px">
                Canc. Nº: {{$order->id}}
            </p>
        </td>
    </tr>
</table>
@endsection

@section('content')
<div>
    <p>
        <b>Cliente</b>: {{$order->client->full_name}}
    </p>
    <p>
        <b>
            @if (!empty($order->client->cpf) && strlen($order->client->cpf) > 14)
            CNPJ @else
            CPF @endif
        </b>: {{!empty($order->client->cpf) ? $order->client->cpf : 'Não informado'}}
    </p>
    <p>
        <b>Vendedor</b>: {{$order->seller->name}}
    </p>
</div>
<div style="margin-top: 10px">
    @if (count($order->coupons) > 0)
    <p>Valor devolvido em cupom: <b>R$ {{number_format($order->coupons[0]->value, 2, ',', '.')}}</b></p>
    <p>Cupom válido até: <b>{{\Carbon\Carbon::parse($order->coupons[0]->end_date)->format('d/m/Y')}} </b></p>
    <p>Código do cupom: <b>{{$order->coupons[0]->name}}</b></p>
    @else
    <p>
        Valor devolvido em dinheiro: <b>R$ {{number_format($order->total, 2, ',', '.')}}</b>
    </p>
    @endif
</div>
@endsection