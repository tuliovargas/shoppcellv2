<?php

namespace App\Services\Checklist;

class UpdateChecklistService
{
    /**
     * @param $data
     * @param $checklist
     * @return mixed
     */
    public function run($data, $checklist)
    {
        $checklist->update($data);

        return $checklist;
    }
}
