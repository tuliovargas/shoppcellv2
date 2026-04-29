<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
			'order_id' => $this->order_id,
			'payment_method_id' => $this->payment_method_id,
			'value' => $this->value,
			'tax_installment_id' => $this->tax_installment_id,
			'brand_card' => $this->brand_card,
			'pix_number'=> $this->pix_number,
			'check_number' => $this->check_number,
			'check_name'=> $this->check_name,
			'bank_id'=> $this->bank_id,
			'cashier_info_id'=> $this->cashier_info_id,
			'charge'=> $this->charge,
			'payment_method'=> $this->payment_method,
		];
	}
}
