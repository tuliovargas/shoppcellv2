<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'invoice' => $this->invoice,
			'payday' => $this->payday,
			'value' => $this->value,
			'installments' => $this->installments,
			'observation' => $this->observation,
			'payment_method_id' => $this->payment_method_id,
			'supplier_id' => $this->supplier_id,
			'expense_type_id' => $this->expense_type_id,
			'cashier_info_id' => $this->cashier_info_id,
		];
	}
}
