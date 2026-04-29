<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Helpers
{
    static function unionReport($table, $sumColumn, $dateColumn, $month, $year, $customSelect = null)
    {
        $start = Carbon::parse("$year-$month")->startOfMonth();
        $end = Carbon::parse("$year-$month")->endOfMonth();
        $today = Carbon::today();
        if ($month == $today->format('m') && $year == $today->format('Y')) {
            $end = $today;
        }
        $period = CarbonPeriod::create($start, $end);

        $sql = "";

        foreach ($period as $date) {
            $select = !empty($customSelect) ? $customSelect : "SUM($sumColumn)";
            $sql .= " SELECT $select AS total FROM $table WHERE DATE($dateColumn) = '" . $date->format("Y-m-d") . "'";
            if ($date->format('d') != $end->format('d')) {
                $sql .= " UNION ALL ";
            }
        }
        return $sql;
    }

    static function unformatCurrency($value)
    {
        return str_replace([".", ","], ["", "."], $value);
    }

    static function file_version($asset)
    {
        $file = str_replace("//", "", $asset);
        $partials = explode("/", $file);
        if (sizeof($partials) < 1) {
            return $file;
        }

        unset($partials[0]);
        $file = $_SERVER['DOCUMENT_ROOT'] . '/' . implode('/', $partials);
        if (!is_file($file)) {
            return $file;
        }
        $version = filemtime($file);
        return $asset . "?v=" . $version;
    }

}
