<?php

namespace App\Services\Address;

use App\Models\Street;

class StreetService
{

    public function run(Array $request) {
        $search = isset($request['search']) ? $request['search'] : '';

        $query = Street::when($search, function ($query, $search) {
            return $query->where('logradouro', 'like', '%' . $search . '%');
        });
        
        if ($request['paginate'] === 'false') {
            return $query->get();
        }

        return $query->paginate(10);
    }
}
