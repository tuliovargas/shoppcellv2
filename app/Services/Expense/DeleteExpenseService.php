<?php

namespace App\Services\Expense;

class DeleteExpenseService
{
    /**
     * @param $expense
     * @return mixed
     */
    public function run($expense)
    {
        return $expense->delete();
    }
}
