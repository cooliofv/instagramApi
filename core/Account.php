<?php


namespace core;


use InstagramAPI\Instagram;
use InstagramAPI\Signatures;

class Account
{

    private $api;
    private $rankToken;

    private $currentUser;

    private $email;
    private $phone;
    private $gender;

    private $messages;


    public function __construct()
    {
        $this->api = new Instagram();
        $this->rankToken = Signatures::generateUUID();

    }


    private function loadUser(){

        $userData = $this->api->account->getCurrentUser();
        $data = json_decode($userData);

        $this->email = $data->user->email;
        $this->phone = $data->user->phone_number;
        $this->gender = $data->user->gender;

        $this->currentUser = new User(
            $data->user->pk,
            $data->user->full_name,
            $data->user->username,
            $data->user->profile_pic_url,
            $this->api,
            $this->rankToken,
            $data->user->external_url,
            $data->user->biography
        );

    }//loadUser

    public static function run(){

        return new self;
    }//run

    public function login($login, $password){

        $this->api->login($login, $password);

        $this->loadUser();

        return $this;
    }//login

    public function getUser(){

        return $this->currentUser;
    }//getUser

    public function getSomeData(){

//        return $this->api->;
    }

    public function PostPhoto($data){

        $this->api->timeline->uploadPhoto($data['picture'], $data['meta']);

    }//Post


}//Account