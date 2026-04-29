<?php

namespace App\Services\Bank;

use App\Models\Bank;

class StoreBankService
{
    /**
     * @var Bank
     */
    private $bank;

    /**
     * StoreBankService constructor.
     * @param Bank $bank
     */
    public function __construct(Bank $bank)
    {
        $this->bank = $bank;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        return $this->bank->create($data);
    }
}
