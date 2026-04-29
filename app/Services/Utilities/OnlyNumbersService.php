<?php

namespace App\Services\Utilities;

class OnlyNumbersService
{
    public function run(String $text)
    {
        if(isset($text)) {
            return preg_replace( '/[^0-9]/', '', $text );
        }

        return null;
    }
}
