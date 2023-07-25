<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
class UserAuth extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|numeric',
            'password' => 'required',
        ]);
   
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()],200);       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
   
        return response()->json( [$success],200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|digits:10',
        ]);
   
        if($validator->fails()){
            return response()->json(['status'=>'fail','error'=>$validator->errors()],200);       
        }

        $user = User::where('phone',$request->phone)->first();
        if($user){
           
            $user['otp'] = rand(100000,999999);
            $user2 =  $user->save();
           // print_r($user);
            $success['status'] = 'success';
            $success['message'] = 'Otp send your mobile number';
            $success['data']['phone'] = $request->phone ;
            $success['data']['token'] =  $user->createToken('MyApp')->accessToken;
            
        }else{
            $input = $request->all();
            $input['otp'] = rand(100000,999999);
            $user = User::create($input);
            $success['status'] = 'success';
            $success['message'] = 'Otp send your mobile number';
            $success['data']['phone'] = $request->phone ;
            $success['data']['token'] =  $user->createToken('MyApp')->accessToken;
            
        }
        return response()->json( $success,200);
    }
    public function verifyOtp(Request $request)
    {


        $response['status']="success";
        $response['message']="Verification Successful";
        return response()->json( $response,200);
        
    }
}
