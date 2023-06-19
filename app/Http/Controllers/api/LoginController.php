<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends BaseController
{
    //

    public function login(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()){
            foreach ($validator->errors()->all() as $msg) {
                if (strlen(trim($msg)) > 1){
                    $this->response_data["message"] = $msg;
                    return $this->sendJsonResponse();
                }
            }
        }
        if (Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
            $user = Auth::user();
            $this->response_data["token"] = $user->createToken('Task')->accessToken;
            $this->response_data["message"] = "You have successfully logged.";
            $this->response_data["data"] = ["user"=>["first_name"=>$user->first_name, "last_name"=>$user->last_name, "email"=>$user->email, "photo"=>$user->full_image]];
            return $this->sendJsonResponse();
        }else{
            $this->response_data["message"] = "Unauthorized";
            return $this->sendJsonResponse(self::HTTP_UNAUTHORIZED);
        }


    }

    public function logout(Request $request):JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
