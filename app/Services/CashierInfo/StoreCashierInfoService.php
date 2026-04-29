<?php

namespace App\Services\CashierInfo;

use App\Models\CashierInfo;
use App\Models\CashierInfoFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreCashierInfoService
{
    /**
     * @var CashierInfo
     */
    private $cashier;
    /**
     * StoreCashierInfoService constructor.
     * @param CashierInfo $cashier
     */
    public function __construct(
        CashierInfo $cashier
    ) {
        $this->cashier = $cashier;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($requestData)
    {
        $cashier = null;

        // \Log::info(json_encode($requestData, JSON_PRETTY_PRINT));

        DB::beginTransaction();
        try{
            $data = [];

            $cashierOpen = $this->cashier->where('close_time', null)->orderBy('created_at', 'desc')->first();
            if ($cashierOpen) {
                $cashierOpen->update(['close_time' => now()]); // Fecha um caixa se estiver um aberto
            }

            $data['deposit'] = $requestData['deposit'] ?? 0;
            $data['observation_open'] = $requestData['observations'] ?? null;
            $data['user_id'] = auth()->user()->id;
            $data['charge'] = 0;

            $cashier = $this->cashier->create($data);

            $files = $requestData['files'] ?? [];

            foreach($files as $file){
                $name = $file["name"];
                $extension = $file["extension"] != 'unknow' ? $file["extension"] : 'txt';
                $mimeType = $file["mimeType"] != 'unknow' ? $file["mimeType"] : 'text/plain';
                $dataFile = $file["data"] ?? '';

                if(strpos($dataFile, 'base64,') !== false){
                    $dataFile = explode('base64,', $dataFile)[1];
                }

                if(empty($dataFile)) continue;

                $path = '/cashier_infos/' . Str::random(40) . '.' . $extension;
                $dataFile = base64_decode($dataFile);
                Storage::put($path, $dataFile);

                $cashierInfoFile = CashierInfoFile::create([
                    'cashier_info_id' => $cashier->id,
                    'mime_type' => $mimeType,
                    'name' => $name,
                    'path' => $path,
                    'is_open' => true
                ]);
            }

            DB::commit();
        } catch(\Throwable | \Exception $e){
            DB::rollback();
            Log::error($e);

            abort(500);
        }

        $cashier->expenses = $cashier->getExpenses(); // carregando despesas dessa data
        return $cashier;
    }
}
