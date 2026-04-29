<?php

namespace App\Services\Expense;

class UpdateExpenseService
{
    /**
     * @param $data
     * @param $expense
     * @return mixed
     */
    public function run($data, $expense)
    {
        $expense->update($data);

        return $expense;
    }
}
