<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails())
            return  $this->validationError(422,false,'Validation errors',$validator->errors());

        if (!auth()->attempt($validator)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $user = auth()->user();
        $data['user']=$user;
        $data['access_token']= $user->createToken('API Token');
        return  $this->formatResponse(200,'success','user login successfully',$data);
    }
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // requires password_confirmation field
        ]);
        if ($validator->fails())
            return  $this->validationError(422,'error','Validation errors',$validator->errors());
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $data['user']=$user;
        $data['access_token'] = $user->createToken('API Token');
        return  $this->formatResponse(200,'success','user login successfully',$data);

    }

}
