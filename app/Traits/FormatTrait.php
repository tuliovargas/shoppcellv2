<?php

namespace App\Traits;

trait FormatTrait
{
    public function brl2decimal($brl, $decimalPlaces = 2)
    {
        if (preg_match('/^\d+\.{1}\d+$/', $brl))
            return (float) number_format($brl, $decimalPlaces, '.', '');
        $brl = preg_replace('/[^\d\.\,]+/', '', $brl);
        $decimal = str_replace('.', '', $brl);
        $decimal = str_replace(',', '.', $decimal);
        return (float) number_format($decimal, $decimalPlaces, '.', '');
    }
}
