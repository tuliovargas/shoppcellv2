<?php

namespace App\Services\ExpenseType;

use App\Exceptions\DeleteNotAllowedException;

class DeleteExpenseTypeService
{
    /**
     * @param $expenseType
     * @return mixed
     * @throws DeleteNotAllowedException
     */
    public function run($expenseType)
    {
        if ($expenseType->expenses->count() > 0) {
            throw new DeleteNotAllowedException();
        }
        return $expenseType->delete();
    }
}
