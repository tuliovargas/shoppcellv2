<?php

namespace App\Services\Report;

use App\Models\Product;
use Carbon\Carbon;
use App\Services\Utilities\Util;
use PDF;
use App\Models\Configuration;

class InventoryReportService
{
    /**
     * @var Product
     */
    private $product;

    /**
     * IndexProductService constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function run($data)
    {
        $is_pdf = $data['is_pdf'] ?? 0;
        $active = $data['active'] ?? '1';
        $min_ini = $data['min_ini'] ?? null;
        $min_fim = $data['min_fim'] ?? null;
        $atual_ini = $data['atual_ini'] ?? null;
        $atual_fim = $data['atual_fim'] ?? null;

        if(!array_key_exists('min_ini', $data)){
            $atual_ini = '0';
            $atual_fim = '0';
        }

        $products = $this->product
            ->when($active != null && $active >= 0, function ($query) use ($active){
                return $query->where('is_active', $active);
            })
            ->when($min_ini != null, function ($query) use ($min_ini){
                return $query->where('minimum_stock', '>=', $min_ini);
            })
            ->when($min_fim != null, function ($query) use ($min_fim){
                return $query->where('minimum_stock', '<=', $min_fim);
            })
            ->when($atual_ini != null, function ($query) use ($atual_ini){
                return $query->where('quantity_in_stock', '>=', $atual_ini);
            })
            ->when($atual_fim != null, function ($query) use ($atual_fim){
                return $query->where('quantity_in_stock', '<=', $atual_fim);
            })
            ->orderBy('name', 'asc')
            ->get();

        if($is_pdf > 0){
            $company_name = Configuration::where('key', 'company_name')->first()->value;
            $address = Configuration::where('key', 'address')->first()->value;
            $cellphone = Configuration::where('key', 'cellphone')->first()->value;
            $email = Configuration::where('key', 'email')->first()->value;

            $pdf = PDF::loadView('reports.pdf_layouts.inventory', [
                'products' => $products,
                'status' => $active != '-1' ? $this->parseStatus($active) : 'Todas as Situações',
                'minStock' => ($min_ini != null || $min_fim != null) ? "Entre $min_ini e $min_fim unidades" : '',
                'actualStock' => ($atual_ini != null || $atual_fim != null) ? "Entre $atual_ini e $atual_fim unidades" : '',
                'company_name' => $company_name,
                'address' => $address,
                'cellphone' => $cellphone,
                'email' => $email,
            ]);

            return $pdf->stream();
        }

        return $products;
    }

    public function parseStatus($status){
        $allStatus = [
            '1' => 'Ativo',
            '0' => 'Inativo'
        ];

        return $allStatus[$status] ?? 'Indeterminado';
    }
}
