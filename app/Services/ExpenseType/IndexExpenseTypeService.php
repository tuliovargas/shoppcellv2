<?php

namespace App\Services\ExpenseType;

use App\Models\ExpenseType;

class IndexExpenseTypeService
{
    /**
     * @var ExpenseType
     */
    private $expenseType;

    /**
     * IndexExpenseTypeService constructor.
     * @param ExpenseType $expenseType
     */
    public function __construct(ExpenseType $expenseType)
    {
        $this->expenseType = $expenseType;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        $search = isset($request['search']) ? $request['search'] : '';

        $query = $this->expenseType->when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });

        if ($request->paginate === 'false') {
            return $query->get();
        }

        return $query->paginate(10);
    }
}
