<?php

namespace App\Services\Cashier;

use App\Models\Cashier;
use Illuminate\Database\Eloquent\Collection;

class IndexCashierService
{
    /**
     * @var Cashier
     */
    private $cashier;

    /**
     * IndexCashierService constructor.
     * @param Cashier $cashier
     */
    public function __construct(Cashier $cashier)
    {
        $this->cashier = $cashier;
    }

    /**
     * @return Cashier[]|Collection
     */
    public function run()
    {
        return $this->cashier->all();
    }
}
