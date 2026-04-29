<?php

namespace App\Services\CashierInfo;

use App\Models\CashierInfo;

class ChangeCashierInfoService
{
    /**
     * @var CashierInfo
     */
    private $cashier;
    /**
     * StoreCashierInfoService constructor.
     * @param CashierInfo $cashier
     */
    public function __construct(
        CashierInfo $cashier
    ) {
        $this->cashier = $cashier;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data, $cashier)
    {
        // \Log::info(json_encode($data, JSON_PRETTY_PRINT));

        $cashier = $this->cashier->where('close_time', null)->orderBy('created_at', 'desc')->first();
        if (!$cashier) return false;

        $difference = $data['difference'] ?? [];
        $data['difference'] = json_encode($difference);

        $cashier = $cashier->update($data);
        if ($cashier) {
            /** @var CashierInfo $cashier */
            $cashier = $this->cashier->where('close_time', null)->orderBy('created_at', 'desc')->first();
            $cashier->expenses = $cashier->getExpenses();
        }
        return $cashier;
    }
}
