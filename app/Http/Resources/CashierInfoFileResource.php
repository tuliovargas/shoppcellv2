<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CashierInfoFileResource extends JsonResource
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
			'cashier_info_id' => $this->cashier_info_id,
			'mime_type' => $this->mime_type,
			'name' => $this->name,
			'path' => $this->path,
			'is_open' => $this->is_open,
		];
	}
}
