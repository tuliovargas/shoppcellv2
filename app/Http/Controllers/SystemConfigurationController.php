<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Services\SystemConfiguration\IndexSystemConfigurationService;
use App\Services\SystemConfiguration\UpdateSystemConfigurationService;

class SystemConfigurationController extends Controller
{

    /**
     * @var IndexSystemConfigurationService
     */
    private $indexSystemConfigurationService;

    /**
     * SystemConfigurationController constructor.
     * @param IndexSystemConfigurationService $indexSystemConfigurationService
     */
    public function __construct(
        IndexSystemConfigurationService $indexSystemConfigurationService
    ) {
        $this->indexSystemConfigurationService = $indexSystemConfigurationService;
    }

    public function index(Request $request)
    {
        $configuration = $this->indexSystemConfigurationService->run($request);
        return response($configuration);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $configuration = Configuration::all();

        $configuration = $configuration->mapWithkeys(function ($item) {
            return [$item['key'] => $item['value']];
        });

        $configuration = (object) $configuration->all();

        return view('configurations.system', compact('configuration'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UpdateSystemConfigurationService $system)
    {
        $data = $request->all();
        $system->run($data);

        return redirect()->back()->with([
            'success' => true
        ]);
    }

}
