<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getMarketplace(){
        $data = Marketplace::get();
        return $this->formatResponse('200','success','Marketplace list successfully',$data);
    }
    public function productServiceByMarketplace($id){

        if ($id==1){
            $data = Service::where('marketplace_id',$id)
                ->with('media')
                ->get();
            if(!$data){
                return $this->validationError('404','success','data not found');
            }

        }
        elseif ($id==1){
            $data = Service::where('marketplace_id',$id)
                ->with('media')
                ->get();
            if(!$data){
                return $this->validationError('404','success','data not found');
            }
        }
        else{
            $data = Product::where('marketplace_id',$id)
                ->with('media')
                ->get();
            if(!$data){
                return $this->validationError('404','success','data not found');
            }

        }
        return $this->formatResponse('200','success','Data list successfully',$data);

    }
}
