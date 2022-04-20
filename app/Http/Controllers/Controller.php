<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    public function parse_parameters( $request1, $api_name){
         // dd($api_name);/* 方法名test*/
         return 'controlTest';
    }
}
