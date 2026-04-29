<?php

namespace App\Services\Bank;

class DeleteBankService
{
    /**
     * @param $bank
     * @return mixed
     */
    public function run($bank)
    {
        return $bank->delete();
    }
}
