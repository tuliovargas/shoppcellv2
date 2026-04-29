<?php

namespace App\Services\Supplier;

class DeleteSupplierService
{
    /**
     * @param $supplier
     * @return mixed
     */
    public function run($supplier)
    {
        $supplier->address()->delete();
        return $supplier->delete();
    }
}
