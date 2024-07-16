<?php

if (!function_exists('formatMoney')){

    function formatMoney($p){
        
        $number = $p;
        $pos = strpos($number, ",");
        
        if($pos != false){
            $number = str_replace('.','',$number);
            $number = str_replace(',','.',$number);        
        }

        return $number;
    }
}