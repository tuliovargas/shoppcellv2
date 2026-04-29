<?php

namespace App\Services\Client;

use App\Models\Client;

class IndexClientService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * IndexClientService constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        $search = isset($request['search']) ? $request['search'] : '';

        $query = $this->client->with(['address'])->when($search, function ($query, $search) {
            return $query->where('full_name', 'like', '%' . $search . '%')->orWhere('cpf', 'like', '%' . $search . '%');
        });

        if ($request->paginate === 'false') {
            return $query->get();
        }

        return $query->paginate(10);
    }
}
