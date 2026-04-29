<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
			'full_name' => $this->full_name,
			'social_name' => $this->social_name,
			'gender' => $this->gender,
			'photo_url' => $this->photo_url,
			'cpf' => $this->cpf,
			'rg' => $this->rg,
			'birthdate' => $this->birthdate,
			'cellphone' => $this->cellphone,
			'phone' => $this->phone,
			'email' => $this->email,
			'observation' => $this->observation,
			'profession' => $this->profession,
			'is_active' => $this->is_active,
		];
	}
}
