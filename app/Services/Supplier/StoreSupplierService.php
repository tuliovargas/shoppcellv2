<?php

namespace App\Services\Supplier;

use App\Models\Supplier;
use App\Services\Address\StoreAddressService;
use App\Services\Utilities\OnlyNumbersService;
use App\Traits\UploadTrait;

class StoreSupplierService
{
    use UploadTrait;

    /**
     * @var Supplier
     */
    private $supplier;

    /**
     * @var OnlyNumbersService
     */
    private $onlyNumbers;

    /**
     * @var StoreAddressService
     */
    private $storeAddressService;


    /**
     * StoreSupplierService constructor.
     * @param Supplier $supplier
     * @param OnlyNumbersService $onlyNumbers
     * @param StoreAddressService $storeAddressService
     */
    public function __construct(Supplier $supplier, OnlyNumbersService $onlyNumbers, StoreAddressService $storeAddressService)
    {
        $this->supplier = $supplier;
        $this->onlyNumbers = $onlyNumbers;
        $this->storeAddressService = $storeAddressService;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        if (isset($data['photo'])) {
            $data['photo_url'] = $this->uploadFile($data['photo'], 'public/suppliers_photos');
        }

        $data['cnpj'] = $this->onlyNumbers->run($data['cnpj']);
        $data['state_registration'] = isset($data['state_registration']) && !empty($data['state_registration']) ? $this->onlyNumbers->run($data['state_registration']) : null;
        $data['cellphone'] = isset($data['cellphone']) && !empty($data['cellphone']) ? $this->onlyNumbers->run($data['cellphone']) : null;
        $data['phone'] = isset($data['phone']) && !empty($data['phone']) ? $this->onlyNumbers->run($data['phone']) : null;

        $this->supplier = $this->supplier->create($data);
        $this->storeAddressService->run($this->supplier, $data);

        return $this->supplier;
    }
}
