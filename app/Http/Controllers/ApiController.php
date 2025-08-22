<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getMarketplace(){
        $data = Marketplace::get();
        return $this->formatResponse('200','success','Marketplace list successfully',$data);
    }
}
