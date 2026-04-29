<?php

namespace App\Services\ExpenseType;

use App\Models\ExpenseType;

class StoreExpenseTypeService
{
    /**
     * @var ExpenseType
     */
    private $expenseType;

    /**
     * StoreExpenseTypeService constructor.
     * @param ExpenseType $expenseType
     */
    public function __construct(ExpenseType $expenseType)
    {
        $this->expenseType = $expenseType;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        return $this->expenseType->create($data);
    }
}
