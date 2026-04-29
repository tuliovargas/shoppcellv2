<?php

namespace App\Services\Supplier;

use App\Models\Supplier;
use App\Services\Address\UpdateAddressService;
use App\Services\Utilities\OnlyNumbersService;
use App\Traits\UploadTrait;

class UpdateSupplierService
{
    use UploadTrait;

    /**
     * @var OnlyNumbersService
     */
    private $onlyNumbers;

    /**
     * @var UpdateAddressService
     */
    private $updateAddressService;

    /**
     * UpdateSupplierService constructor.
     * @param OnlyNumbersService $onlyNumbers
     * @param UpdateAddressService $updateAddressService
     */
    public function __construct(OnlyNumbersService $onlyNumbers, UpdateAddressService $updateAddressService)
    {
        $this->onlyNumbers = $onlyNumbers;
        $this->updateAddressService = $updateAddressService;
    }

    /**
     * @param Supplier $supplier
     * @param array $data
     * @return Supplier
     */
    public function run(Supplier $supplier, array $data)
    {
        if (isset($data['photo'])) {
            $actualPhoto = storage_path() . '/' . $supplier->photo_url;
            if (file_exists($actualPhoto) && !is_dir($actualPhoto)) {
                unlink($actualPhoto);
            }
            $data['photo_url'] = $this->uploadFile($data['photo'], 'public/suppliers_photos');
        }

        $data['cnpj'] = $this->onlyNumbers->run($data['cnpj']);
        $data['state_registration'] = isset($data['state_registration']) && !empty($data['state_registration']) ? $this->onlyNumbers->run($data['state_registration']) : null;
        $data['cellphone'] = isset($data['cellphone']) && !empty($data['cellphone']) ? $this->onlyNumbers->run($data['cellphone']) : null;
        $data['phone'] = isset($data['phone']) && !empty($data['phone']) ? $this->onlyNumbers->run($data['phone']) : null;

        $supplier->update($data);
        $this->updateAddressService->run($supplier, $data);

        return $supplier;
    }
}
