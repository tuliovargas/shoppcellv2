<?php

namespace App\Services\TaxInstallment;

use App\Models\TaxInstallment;

class UpdateTaxInstallmentService
{
    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        foreach($data as $index => $tax) {
            $taxInstallment = TaxInstallment::where(['quantity' => $index])->update(['interest_rates' => $tax]);
        }
    }
}
