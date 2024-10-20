<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\CustomUserModel;


class AuthController extends ResourceController
{
    //protected $modelName = 'App\Models\CustomUserModel';
    protected $format = 'json';

    //User register methode [post] -> username, email, name, password, gender, phone_no
    public function register()
    {
        //request parameters
        $validationRules =[
            'username' => 'required',
            'email' => 'required',
            'name' => 'required',
            'password' => 'required',
            'gender' => 'required|in_list[male,female]'

        ];
        if (!$this->validate($validationRules)) {
            return $this->respond([
                "status" => false,
                "message" => 'Registration failed due to invalid entries',
                "errors" => $this->validator->getErrors()
            ]);
        }
        $modelobject = new CustomUserModel();
        $entityObject = new User([
            "name" => $this->request->getVar('name'),
            "username" => $this->request->getVar('username'),
            "email" => $this->request->getVar('email'),
            "password" => $this->request->getVar('password'),
            "gender" => $this->request->getVar('gender'),
            "phone_no" => $this->request->getVar('phone_no')
        ]);
        if($modelobject->save($entityObject)){
            return $this->respond([
                "status" => true,
                "message" => 'Registration successful'
            ]);
        }else{
            return $this->respond([
                "status" => false,
                "message" => 'Registration failed'
            ]);
        };
    }
    //User login methode [post] -> email, password

    public function login(){

        $validationRules = [
            "email" => "required",
            "password" => "required"
        ];

        if(!$this->validate($validationRules)){

            return $this->respond([
                "status" => false,
                "message" => "Login Failed",
                "errors" => $this->validator->getErrors()
            ]);
        }

        // Check User Details
        $credentials = [
            "email" => $this->request->getVar("email"),
            "password" => $this->request->getVar("password")
        ];

        try{

            if(auth()->loggedIn()){

                auth()->logout();
            }

            $loginAttempt = auth()->attempt($credentials);

            if(!$loginAttempt->isOK()){

                return $this->respond([
                    "status" => false,
                    "message" => "Login Failed"
                ]);
            } else{

                $userId = auth()->user()->id;

                $shieldModelObject = new UserModel;

                $userInfo = $shieldModelObject->findById($userId);

                $tokenInfo = $userInfo->generateAccessToken("12345678sfgfdgffd");

                $raw_token = $tokenInfo->raw_token;

                return $this->respond([
                    "status" => true,
                    "message" => "User logged in",
                    "token" => $raw_token
                ]);
            }
        } catch (Exception $ex){

            return $this->respond([
                "status" => false,
                "message" => $ex->getMessage()
            ]);
        }
    }

    //User profile methode [get] -> Protected api methode ->auth token value
    public function profile(){
        $userData = auth("tokens")->user();

        return $this->respond([
            "status" => true,
            "message" => "Profile information",
            "data" => $userData
        ]);
    }
    //logout methode [get] -> Protected api methode ->auth token value
    public function logout(){
        auth()->logout();
        auth()->user()->revokeAllAccessTokens();

        return $this->respond([
            "status" => true,
            "message" => "User logged out"
        ]);
    }
}
