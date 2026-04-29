<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductStockLogResource extends JsonResource
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
			'old' => $this->old,
			'new' => $this->new,
			'type' => $this->new > $this->old ? 'Entrada' : 'Saída',
			'user' => [
				"name" => $this->user->name,
				"id" => $this->user->id,
			],
			'date' => $this->created_at->format('d/m/Y H:i'),
		];
	}
}
