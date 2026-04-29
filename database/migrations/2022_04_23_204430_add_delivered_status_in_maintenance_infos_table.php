<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDeliveredStatusInMaintenanceInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
				DB::statement("ALTER TABLE `maintenance_infos` CHANGE `os_status` `os_status` ENUM(
						'waiting_approval',
						'approved',
						'waiting_stock',
						'maintenance',
						'no_maintenance',
						'no_maintenance_delivered',
						'fixed',
						'finished',
						'cancelled'
						);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       DB::statement("ALTER TABLE `maintenance_infos` CHANGE `os_status` `os_status` ENUM(
						 'waiting_approval',
						'approved',
						'waiting_stock',
						'maintenance',
						'no_maintenance',
						'fixed',
						'finished',
						'cancelled');");
    }
}
