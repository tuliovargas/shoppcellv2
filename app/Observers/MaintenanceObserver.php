<?php

namespace App\Observers;

use App\Models\MaintenanceInfo;

class MaintenanceObserver
{
    /**
     * Handle the MaintenanceInfo "created" event.
     *
     * @param  \App\Models\MaintenanceInfo  $maintenanceInfo
     * @return void
     */
    public function created(MaintenanceInfo $maintenanceInfo)
    {
        //
    }

    /**
     * Handle the MaintenanceInfo "updated" event.
     *
     * @param  \App\Models\MaintenanceInfo  $maintenanceInfo
     * @return void
     */
    public function updating(MaintenanceInfo $maintenanceInfo)
    {
        if ($maintenanceInfo->isDirty('os_status')) {
            if ($maintenanceInfo->os_status == 'finished' ) {

            }
        }
    }

    /**
     * Handle the MaintenanceInfo "deleted" event.
     *
     * @param  \App\Models\MaintenanceInfo  $maintenanceInfo
     * @return void
     */
    public function deleted(MaintenanceInfo $maintenanceInfo)
    {
        //
    }

    /**
     * Handle the MaintenanceInfo "restored" event.
     *
     * @param  \App\Models\MaintenanceInfo  $maintenanceInfo
     * @return void
     */
    public function restored(MaintenanceInfo $maintenanceInfo)
    {
        //
    }

    /**
     * Handle the MaintenanceInfo "force deleted" event.
     *
     * @param  \App\Models\MaintenanceInfo  $maintenanceInfo
     * @return void
     */
    public function forceDeleted(MaintenanceInfo $maintenanceInfo)
    {
        //
    }
}
