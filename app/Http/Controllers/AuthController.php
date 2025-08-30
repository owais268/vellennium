<?php

namespace App\Http\Controllers;

use App\Models\BusinessHours;
use App\Models\User;
use App\Models\UserInformation;
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

        if (!auth()->attempt($validator->validate())) {
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

    public function sellerRegistration(Request $request){
        $validator = Validator::make($request->all(), [
            'marketplace_id'   => 'required|string',
            'business_name'   => 'required|string|max:255',
            'business_email'  => 'required|email',
            'business_phone'  => 'required|string|max:20',
            'password'        => 'required|min:6|confirmed',
            'address'         => 'required|string|max:255',
            'country'         => 'required|string|max:100',
            'state'           => 'required|string|max:100',
            'city'            => 'required|string|max:100',
            'zipcode'         => 'required|string|max:20',
            'government_id'   => 'required|file|mimes:jpg,png,pdf|max:2048',
            'documentation_licensing'   => 'required|file|mimes:jpg,png,pdf,docx|max:4096',
        ]);
        if ($validator->fails()) {
            return  $this->validationError(422,'error','Validation errors',$validator->errors());
        }
        $user = User::create([
            'name'     => $request->business_name,
            'email'    => $request->business_email,
            'password' => Hash::make($request->password),
        ]);
        $userInformationData = array_merge($request->all(), [
            'user_id' =>$user->id,
        ]);
        $userInformation = UserInformation::create($userInformationData);
        if ($request->hasFile('government_id')) {
            $userInformation->addMediaFromRequest('government_id')
                ->toMediaCollection('government_id');
        }
        if ($request->hasFile('documentation_licensing')) {
            $userInformation->addMediaFromRequest('documentation_licensing')->toMediaCollection('documentation');
        }
        return $this->formatResponse(200,'success','user details inserted sucessfully',$userInformation);

    }

    public function businessHour($user_id,Request $request){
        $validator = Validator::make($request->all(), [
            'hours' => 'required|array',
            'hours.*.day' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'hours.*.start_time' => 'nullable|date_format:H:i',
            'hours.*.end_time' => 'nullable|date_format:H:i|after:hours.*.start_time',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        foreach ($request->hours as $hour) {
            BusinessHours::updateOrCreate(
                ['seller_id' => $user_id, 'day' => $hour['day']],
                [
                    'start_time' => $hour['start_time'] ?? null,
                    'end_time' => $hour['end_time'] ?? null
                ]
            );
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Business hours saved successfully',
            'data' => null
        ]);

    }

}
