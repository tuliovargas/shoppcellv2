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
        $clientId = $request['clientId'] ?? null;
        if ($clientId !== null && $clientId !== '' && $clientId !== 'null') {
            return $this->client->newQuery()
                ->with(['address'])
                ->where('id', $clientId)
                ->limit(1)
                ->get();
        }

        $search = isset($request['search']) ? trim($request['search']) : '';

        $query = $this->client->with(['address'])->when($search, function ($query, $search) {
            return $query->where('full_name', 'like', '%' . $search . '%')->orWhere('cpf', 'like', '%' . $search . '%');
        });

        if ($request->paginate === 'false') {
            if (!empty($search)) {
                return $query->orderBy('full_name')->limit(30)->get();
            }

            return $query->get();
        }

        return $query->paginate(10);
    }
}
