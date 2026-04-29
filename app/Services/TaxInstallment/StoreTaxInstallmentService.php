<?php

namespace App\Services\TaxInstallment;

use App\Models\TaxInstallment;

class StoreTaxInstallmentService
{
    /**
     * @var TaxInstallment
     */
    private $taxInstallment;

    /**
     * StoreTaxInstallmentService constructor.
     * @param TaxInstallment $taxInstallment
     */
    public function __construct(TaxInstallment $taxInstallment)
    {
        $this->taxInstallment = $taxInstallment;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        return $this->taxInstallment->create($data);
    }
}
