<?php

namespace App\Services\Checklist;

use App\Models\Checklist;

class StoreChecklistService
{
    /**
     * @var Checklist
     */
    private $checklist;

    /**
     * StoreChecklistService constructor.
     * @param Checklist $checklist
     */
    public function __construct(Checklist $checklist)
    {
        $this->checklist = $checklist;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        return $this->checklist->create($data);
    }
}
