<?php
use Illuminate\Support\Facades\Session;


if (!function_exists('getCompany')){

    function getCompany($user){

        if(!is_null($user)){
            if(Session::has('user.company')){
                $company_id = Session::get('user.company');
            }else{
                $company_id = $user->company_id;
                Session::put('user.company', $company_id);            
            }
        }

        return $company_id;
    }
}