<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    function formatResponse($code=200,$status,$message,$data=[]){
        return[
            "status"=>$status,
            "code"=>$code,
            "message"=>$message,
            "data"=>$data,
        ];
    }
    function validationError($code=200,$status,$message,$error=[]){
        return[
            "status"=>$status,
            "code"=>$code,
            "message"=>$message,
            "error"=>$error,
        ];
    }
}
