<?php

namespace App\Services\Bank;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Collection;

class IndexBankService
{
    /**
     * @var Bank
     */
    private $bank;

    /**
     * IndexBankService constructor.
     * @param Bank $bank
     */
    public function __construct(Bank $bank)
    {
        $this->bank = $bank;
    }

    /**
     * @return Bank[]|Collection
     */
    public function run()
    {
        return $this->bank->all();
    }
}
