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

    private function loadFollowers(){

        $followersData = $this->api->people->getFollowers($this->currentUser->id, $this->rankToken);

        $followers = json_decode($followersData);

        foreach($followers->users as $follower){

            $this->currentUser->followers[] = new User(
                $follower->pk,
                $follower->full_name,
                $follower->username,
                $follower->profile_pic_url
                );

        }

    }//loadFollowInfo

    private function loadFollowings(){

        $followingData = $this->api->people->getFollowing($this->currentUser->id, $this->rankToken);

        $following = json_decode($followingData);

        foreach ($following->users as $fellow){

            $this->currentUser->following[] = new User(
                $fellow->pk,
                $fellow->full_name,
                $fellow->username,
                $fellow->profile_pic_url
            );

        }

    }//loadFollowings

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
            $data->user->external_url,
            $data->user->biography
        );

        $this->loadFollowers();
        $this->loadFollowings();

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


}//Account