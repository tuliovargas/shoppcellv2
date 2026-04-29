<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
			'client_id' => $this->client_id,
			'subtotal' => $this->subtotal,
			'discount' => $this->discount,
			'total' => $this->total,
			'status' => $this->status,
			'user_id' => $this->user_id,
			'note' => $this->note,
			'delivery_date' => $this->delivery_date,
			'seller_id' => $this->seller_id,
			'canceled_user_id' => $this->canceled_user_id,
			'canceled_at' => $this->canceled_at,
			'cancellation_observation' => $this->cancellation_observation,
			'is_warranty' => $this->is_warranty,
			'coupon_id' => $this->coupon_id,
			'cashier_info_id' => $this->cashier_info_id,
			'order_id' => $this->order_id,
			'created_at' => $this->created_at,
			'user' => new UserResource($this->user),
			'seller' => new UserResource($this->seller),
			'client' => new ClientResource($this->user),
			'payments' => PaymentResource::collection($this->payments),
		];
	}
}
