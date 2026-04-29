<?php

namespace App\Services\Checklist;

use App\Models\Checklist;

class IndexChecklistService
{
    /**
     * @var Checklist
     */
    private $checklist;

    /**
     * IndexChecklistService constructor.
     * @param Checklist $checklist
     */
    public function __construct(Checklist $checklist)
    {
        $this->checklist = $checklist;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run()
    {
        $query = $this->checklist;
        return $query->all();
    }
}
