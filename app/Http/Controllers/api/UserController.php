<?php

namespace App\Http\Controllers\api;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Http\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    //

    public function index():JsonResponse
    {
        $users = User::all();
        $list = [];
        foreach ($users as $user) {
            $list[] = [
                "id" => $user->id,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "email" => $user->email,
                "photo" => $user->full_image
            ];

        }
        $this->response_data["data"] = ["user"=>[$list]];
        return $this->sendJsonResponse();
    }

    public function save(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => ['required', Password::default()],
            'photo' => 'required|mimes:jpeg,png,jpg,gif'
        ]);



        if ($validator->fails()){
            foreach ($validator->errors()->all() as $msg) {
                if (strlen(trim($msg)) > 1){
                    $this->response_data["message"] = $msg;
                    return $this->sendJsonResponse();
                }
            }
        }

        $photo = "0";
        if ($request->hasFile("photo")){
            $photo = Helper::file_upload($request, 'photo', "user");
        }

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->photo = $photo;
        $user->save();

        $this->response_data["status"] = true;
        $this->response_data["data"] = ["user"=>["id"=>$user->id, "first_name"=>$user->first_name, "last_name"=>$user->last_name, "email"=>$user->email, "photo"=>$user->full_image]];
        return $this->sendJsonResponse();
    }


    public function edit($id):JsonResponse
    {
        $user = User::find($id);
        if (!empty($user)){
            $this->response_data["status"] = true;
            $this->response_data["data"] = ["user"=> ["id"=>$user->id, "first_name"=>$user->first_name, "last_name"=>$user->last_name, "email"=>$user->email, "photo"=>$user->full_image]];
        }else{
            $this->response_data["message"] = "Data not found.";
        }
        return $this->sendJsonResponse();
    }

    public function update(Request $request, $id):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,'.$id,
            'password' => 'required|string',
            'photo' => 'nullable|mimes:jpeg,png,jpg,gif'
        ]);
        if ($validator->fails()){
            foreach ($validator->errors()->all() as $msg) {
                if (strlen(trim($msg)) > 1){
                    $this->response_data["message"] = $msg;
                    return $this->sendJsonResponse();
                }
            }
        }
        $user = User::find($id);
        if (!empty($user)){
            if ($request->hasFile("photo")){
                if ($user->full_image != ""){
                    Storage::delete("public/user/". $user->photo);
                }
                $user->photo = Helper::file_upload($request, 'photo', "user");
            }
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->update();
            $this->response_data["status"] = true;
            $this->response_data["data"] = ["user"=>["id"=>$user->id, "first_name"=>$user->first_name, "last_name"=>$user->last_name, "email"=>$user->email, "photo"=>$user->full_image]];
            return $this->sendJsonResponse();
        }else{
            $this->response_data["message"] = "Data not found.";
            return $this->sendJsonResponse();
        }
    }

    public function delete(Request $request, $id):JsonResponse
    {
        $user = User::find($id);
        if (!empty($user)){
            $user->delete();
            if ($user->full_image != ""){
                Storage::delete("public/user/". $user->photo);
            }
            $this->response_data["status"] = true;
            $this->response_data["message"] = $user->full_name." delete successfully.";
            return $this->sendJsonResponse();
        }else{
            $this->response_data["message"] = "Data not found.";
            return $this->sendJsonResponse();
        }
    }

}
