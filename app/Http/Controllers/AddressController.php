<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Services\Address\StreetService;

class AddressController extends Controller
{

    /**
     * @var StreetService
     */
    private $streetService;

    /**
     * AddressController constructor.
     * @param StreetService $streetService
     */
    public function __construct(
        StreetService $streetService
    ) {
        $this->streetService = $streetService;
    }

    public function streets(Request $request)
    {
        $streets = $this->streetService->run($request->all());
        return response()->json($streets);
    }

}
