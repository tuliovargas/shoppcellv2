<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Modules\ThermalPrinter;

use QrCode;

class PrintController extends Controller
{
    private $qrCode;
    public function __construct()
    {
        $this->qrCode = QrCode::size(300)->generate('https://www.instagram.com/shoppcelloficial/');;
    }
    public function order($id)
    {
        $order = Order::query()
            ->findOrFail($id);
        $data = [
            'order' => $order,
            'qrCode' => $this->qrCode,
            'title' => 'Pedido',
            'id' => $order->id
        ];

        return ThermalPrinter::print('print.order', $data);
    }

    public function cancellation($id)
    {
        $order = Order::query()
            ->where('status', '=', 'returned')
            ->findOrFail($id);
        $data = [
            'order' => $order,
            'qrCode' => $this->qrCode,
            'title' => 'Cancelamento de pedido',
            'id' => $order->id
        ];

        return ThermalPrinter::print('print.cancellation', $data);
    }
}
