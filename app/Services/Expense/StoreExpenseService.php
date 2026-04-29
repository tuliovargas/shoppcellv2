<?php

namespace App\Services\Expense;

use App\Models\Cashier;
use App\Models\CashierInfo;
use App\Models\Expense;
use App\Models\ExpenseReceipt;
use Illuminate\Http\UploadedFile;

class StoreExpenseService
{
    /**
     * @var Expense
     */
    private $expense;

    /**
     * StoreExpenseService constructor.
     * @param Expense $expense
     */
    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        $cashier = CashierInfo::query()->whereNull('close_time')->orderBy('id', 'DESC')->first();
        $data['cashier_info_id'] = !empty($cashier) ? $cashier->id : null;
        $expenseObject = $this->expense->create($data);
        if ($expenseObject && isset($data['receipt'])) {
            /** @var UploadedFile $receipt */
            foreach ($data['receipt'] as $receipt) {
                $stored = $receipt->store('public/expense_receipts');
                if ($stored) {
                    ExpenseReceipt::create(
                        [
                            'path' => $stored,
                            'expense_id' => $expenseObject->id
                        ]
                    );
                }
            }
        }
        return $expenseObject;
    }
}
