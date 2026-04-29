<?php

namespace App\Services\Bank;

class UpdateBankService
{
    /**
     * @param $data
     * @param $bank
     * @return mixed
     */
    public function run($data, $bank)
    {
        $bank->update($data);

        return $bank;
    }
}
