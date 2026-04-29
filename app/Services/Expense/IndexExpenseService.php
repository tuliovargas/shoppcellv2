<?php

namespace App\Services\Expense;

use App\Models\Expense;

class IndexExpenseService
{
    /**
     * @var Expense
     */
    private $expense;

    /**
     * IndexExpenseService constructor.
     * @param Expense $expense
     */
    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        $search = isset($request['search']) ? $request['search'] : '';

        $query = $this->expense->when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });

        if ($request->paginate === 'false') {
            return $query->get();
        }

        return $query->paginate(50);
    }
}
