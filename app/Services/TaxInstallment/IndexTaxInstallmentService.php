<?php

namespace App\Services\TaxInstallment;

use App\Models\TaxInstallment;
use Illuminate\Database\Eloquent\Collection;

class IndexTaxInstallmentService
{
    /**
     * @var TaxInstallment
     */
    private $taxInstallment;

    /**
     * IndexTaxInstallmentService constructor.
     * @param TaxInstallment $taxInstallment
     */
    public function __construct(TaxInstallment $taxInstallment)
    {
        $this->taxInstallment = $taxInstallment;
    }

    /**
     * @return TaxInstallment[]|Collection
     */
    public function run()
    {
        return $this->taxInstallment->all();
    }
}
