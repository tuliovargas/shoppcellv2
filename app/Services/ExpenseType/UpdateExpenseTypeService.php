<?php

namespace App\Services\ExpenseType;

class UpdateExpenseTypeService
{
    /**
     * @param $data
     * @param $expenseType
     * @return mixed
     */
    public function run($data, $expenseType)
    {
        $expenseType->update($data);

        return $expenseType;
    }
}
