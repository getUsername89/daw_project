<?php

class Utils {
    public static function getAcademicYear($date) {
        $time=strtotime($date);
    
        $month=date("F", $time);
    
        $year=date("Y", $time);
    
        switch ($month) {
            case '01':
            case '02':
            case '03':
            case '04':
            case '05':
            case '06':
                $year--;
                break;
        }
    
        return $year;
    }
    
    public static function capitalizeText($string, $everyWord = false) {
        if ($everyWord) {
            $explodedString = preg_split("/\s/", $string);
            foreach ($explodedString as $key => $value) {
                $explodedString[$key][0] = strtoupper($value[0]);
            }
            $string = implode(" ", $explodedString);
        } else {
            $string[0] = strtoupper($string[0]);
        }
        
        return $string;
    }
    
    public static function cleanGetData($var)
    {
        if (isset($_REQUEST[$var]))
            $tmp=strip_tags(self::sinEspacios($_REQUEST[$var]));
        else {
            $tmp= "";
        }
                
        return $tmp;
    }
    
    public static function sinEspacios($frase) {
        $texto = trim(preg_replace('/ +/', ' ', $frase));
        return $texto;
    }
    
    public static function generateRandomKey($length = 75) {
        $token = '';
        $keys = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
    
        for ($i = 0; $i < $length; $i++) {
            $token .= $keys[array_rand($keys)];
        }
    
        return $token;
    }
}