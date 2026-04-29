<?php

namespace App\Services\TaxInstallment;

class DeleteTaxInstallmentService
{
    /**
     * @param $taxInstallment
     * @return mixed
     */
    public function run($taxInstallment)
    {
        return $taxInstallment->delete();
    }
}
