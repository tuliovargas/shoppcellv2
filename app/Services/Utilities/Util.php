<?php

namespace App\Services\Utilities;

use Carbon\Carbon;
use Log;

class Util
{
    public static function mask($val, $mask){
        $val = str_replace(['-','.', '/'], '', $val);
        $maskared = '';
        $k = 0;
        for($i = 0; $i <= strlen($mask)-1; $i++) {
            if($mask[$i] == '#'){
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    public static function removerAcentos($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/","/(`)/"), explode(" ","a A e E i I o O u U n N c C '"), $string);
    }

    public static function removerCaracteresEspeciais($string){
        return preg_replace("/[^a-z0-9\_\-\. ]/i", '', $string);
    }

    public static function normalizarString($string){
        $string = self::removerAcentos($string);
        $string = self::removeMask($string);
        $string = self::removerCaracteresEspeciais($string);
        return $string;
    }

    public static function removeMask($input){
        $input_copy = preg_replace("/[^a-zA-Z0-9]/", "", trim($input));
        if(is_numeric($input_copy)){
            return $input_copy; // no sistema só tiramos mascara de numeros (telefone, cpf, cnpj, cep, etc).
        }

        return trim($input); 
    }

    public static function monthToHuman($mes){
        switch ($mes){
            case 1: return 'Janeiro';
            case 2: return 'Fevereiro';
            case 3: return 'Março';
            case 4: return 'Abril';
            case 5: return 'Maio';
            case 6: return 'Junho';
            case 7: return 'Julho';
            case 8: return 'Agosto';
            case 9: return 'Setembro';
            case 10: return 'Outubro';
            case 11: return 'Novembro';
            default: return 'Dezembro';
        }
    }

    public static function monthToHumanAbr($mes){
        switch ($mes){
            case 1: return 'Jan';
            case 2: return 'Fev';
            case 3: return 'Mar';
            case 4: return 'Abr';
            case 5: return 'Mai';
            case 6: return 'Jun';
            case 7: return 'Jul';
            case 8: return 'Ago';
            case 9: return 'Set';
            case 10: return 'Out';
            case 11: return 'Nov';
            default: return 'Dez';
        }
    }

    public static function humanAbrToMonth($humanAbr){
        $humanAbr = strtolower($humanAbr);

        switch ($humanAbr){
            case 'jan': return 1;
            case 'fev': return 2;
            case 'mar': return 3;
            case 'abr': return 4;
            case 'mai': return 5;
            case 'jun': return 6;
            case 'jul': return 7;
            case 'ago': return 8;
            case 'set': return 9;
            case 'out': return 10;
            case 'nov': return 11;
            default: return 12;
        }
    }

    public static function stringToDouble($string){
        if(is_numeric($string)){
            return floatval($string);
        }
        
        $value = str_replace('.', '', $string);
        $value = str_replace(',', '.', $value);

        if(is_numeric($value)){
            return floatval($value);
        }

        return 0;
    }

    public static function doubleToString($value, $decimais = 2){
        if($value == null || !is_numeric($value)){
            return number_format(0.0, $decimais, ',', '.');
        }

        return number_format($value, $decimais, ',', '.');
    }

    public static function minutesToHour($time, $format = '%02d:%02d'){
        if(!$time || $time < 1){
            return '00:00';
        }

        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    public static function secondsToHour($time){
        if(!$time || $time < 1){
            return '00:00:00';
        }

        $horas = self::truncate($time/60/60);
        $horas = str_pad($horas, 2, "0", STR_PAD_LEFT);
        return gmdate("$horas:i:s", $time);
    }

    public static function truncate($val, $f="0"){
        if(($p = strpos($val, '.')) !== false) {
            $val = floatval(substr($val, 0, $p + 1 + $f));
        }
        return $val;
    }

    public static function hourToMinutes($hour){
        $time = explode(':', $hour);
        return ($time[0]*60) + ($time[1]);
    }


    public static function IsCellPhoneWithDDD($cellPhone) : bool
    {
        return count(self::removeMask($cellPhone)) === 11;
    }

    public static function formatCellPhoneForTwilio($cellPhone)
    {
        return '+55' . preg_replace('/\D/', '', $cellPhone);
    }

    public static function diffDateToHuman(Carbon $date_one, Carbon $date_two){
        $diffInDays = $date_one->diffInDays($date_two);
        $diffInHours = $date_one->diffInHours($date_two) - ($diffInDays * 24);
        $diffInMinutes = $date_one->diffInMinutes($date_two) - ($diffInDays * 24 * 60) - ($diffInHours * 60);

        $formated = '';

        if($diffInDays > 0){
            $formated .= $diffInDays;
            $formated .= $diffInDays > 1 ? ' dias' : ' dia';
        }

        if($diffInHours > 0){
            $formated .= strlen($formated) > 0 ? ', ' : '';
            $formated .= $diffInHours;
            $formated .= $diffInHours > 1 ? ' horas' : ' hora';
        }

        if($diffInMinutes > 0){
            $formated .= strlen($formated) > 0 ? ' e ' : '';
            $formated .= $diffInMinutes;
            $formated .= $diffInMinutes > 1 ? ' minutos' : ' minuto';
        }


        if(strlen($formated) == 0){
            $formated = 'Menos de 1 minuto';
        }

        return $formated;
    }


    public static function formatarTelefone($phone){
        $phone = self::normalizarString($phone);
        $length = strlen($phone);
        $mask = '';

        switch($length){
            case 13: $mask = '+## (##) #####-####'; break;
            case 12: $mask = '+## (##) ####-####'; break;
            case 11: $mask = '(##) #####-####'; break;
            case 10: $mask = '(##) ####-####'; break;
            case 8: $mask = '####-####'; break;
            case 9: $mask = '#####-####'; break;
            default: $mask = '##################'; break;
        }

        $phone = self::mask($phone, $mask);
        return $phone;
    }


    public static function formatarCpfCnpj($cpfCnpj){
        $cpfCnpj = self::normalizarString($cpfCnpj);
        $length = strlen($cpfCnpj);
        $mask = '';

        if($length > 0){
            $mask = $length > 11 ? '##.###.###/####-##' : '###.###.###-##';
        }

        $cpfCnpj = self::mask($cpfCnpj, $mask);
        return $cpfCnpj;
    }

    public static function formatarCep($cep){
        $cep = self::normalizarString($cep);
        $length = strlen($cep);
        $mask = $length > 0 ? '#####-###' : '';
        
        $cep = self::mask($cep, $mask);
        return $cep;
    }

    public static function delTree($dir) {
        try{
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
            }

            return rmdir($dir);
        } catch(\Exception $e){
            Log::error($e);
        }

        return false;
    }

    public static function isValidEmail($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isWorkDay(Carbon $date){
        return !$date->isSunday() && self::isValidDay($date);
    }

    public static function isUtilDay(Carbon $date){
        return !$date->isWeekend() && self::isValidDay($date);
    }
}