<?php

namespace App\Services\Address;

class UpdateAddressService
{

    public function run($owner, array $data)
    {

        $data['postcode'] = isset($data['postcode']) ? preg_replace('/[^0-9]/', '', $data['postcode']) : null;

        $address = [
            'postcode' => isset($data['postcode']) ? $data['postcode'] : null,
            'street' => isset($data['street']) ? $data['street'] : null,
            'neighborhood' => isset($data['neighborhood']) ? $data['neighborhood'] : null,
            'complement' => isset($data['complement']) ? $data['complement'] : null,
            'number' => isset($data['number']) ? $data['number'] : null,
            'city' => isset($data['city']) ? $data['city'] : null,
            'state' => isset($data['state']) ? $data['state'] : null,
        ];

        $owner->address()->update($address);
    }
}
