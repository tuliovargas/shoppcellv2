@extends('print.layout')

@section('header')
<table class="border-t">
    <tr>
        <td>
            <p class="left">
                {{$order->created_at->format('d/m/Y H:i')}}
            </p>
        </td>
        <td>
            <p class="right" style="font-size: 12px">
                Venda Nº: {{$order->id}}
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
        @if(@$order->seller->name)
            <b>Vendedor</b>: {{$order->seller->name}}
        @endif
    </p>
</div>
<div style="margin-top: 15px">
    <table class="border-t">
        <thead>
            <tr>
                <th>
                    Qnt.
                </th>
                <th>
                    Produto
                </th>
                <th>
                    Preço Un.
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->products as $product)
                <tr>
                    <td>
                        {{$product->amount}} X
                    </td>
                    <td>
                        {{$product->product->name}}
                    </td>
                    <td>
                        R$ {{number_format($product->price + $product->addition - $product->discount, 2, ',', '.')}}
                    </td>
                </tr>

                @foreach ($product->byProducts as $byProduct)
                    <tr>
                        <td>
                            {{$byProduct->amount}} X
                        </td>
                        <td>
                            {{$byProduct->product->name}}
                        </td>
                        <td>
                            R$ {{number_format($byProduct->price + $byProduct->addition - $byProduct->discount, 2, ',', '.')}}
                        </td>
                    </tr>
                @endforeach

            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="right border-t">
                    <p>
                        <b>Subtotal</b>: R$ {{ number_format($order->subtotal, 2, ',', '.') }}
                    </p>
                    @if ($order->coupon)
                    <p>
                        <b>Cupom utilizado</b> ({{$order->coupon->name}}): R$
                        {{ number_format($order->coupon->value, 2, ',', '.') }}
                    </p>
                    @endif
                    @if ($order->discount != 0)
                    <p>
                        <b>Desconto:</b> R$ {{ number_format($order->discount, 2, ',', '.') }}
                    </p>
                    @endif
                    <p>
                        <b>Total</b>: R$ {{ number_format($order->total, 2, ',', '.') }}
                    </p>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@if ($order->note)
<div style="margin-top: 15px" class="border-t">
    <h2>
        Observações:
    </h2>
    <p>
        {!! str_replace("\n", "<br />", $order->note)!!}
    </p>
</div>
@endif
<div style="margin-top: 15px" class="border-t">
    <p>
        Este comprovante é válido como garantia de sua venda, guarde-o.
    </p>
</div>
@endsection