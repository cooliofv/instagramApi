<?php


namespace core;


class User{

    private $id;
    private $full_name;
    private $username;
    private $profile_pic_url;
    private $website;
    private $biography;
    private $posts;

    private $followers;
    private $following;
    private $api;
    private $rankToken;

    /**
     * User constructor.
     * @param $data array associative array with correct keys to assign class fields
     */
    public function __construct($data)
    {

        foreach ($data as $key => $value) {

            $this->{$key} = isset($data[$key]) ? $data[$key] : null;
        }//foreach
    }//__constructor

    private function loadFollowers(){

        $followersData = $this->api->people->getFollowers($this->id, $this->rankToken);

        $followers = json_decode($followersData);

        foreach($followers->users as $follower){

            $data = [
                'id'              => $follower->pk,
                'full_name'       => $follower->full_name,
                'username'        => $follower->username,
                'profile_pic_url' => $follower->profile_pic_url,
                'api'             => $this->api,
                'rankToken'       => $this->rankToken
            ];

            $this->followers[] = new User($data);

        }//foreach
    }//loadFollowInfo

    private function loadFollowings(){

        $followingData = $this->api->people->getFollowing($this->id, $this->rankToken);

        $following = json_decode($followingData);

        foreach ($following->users as $fellow){

            $data = [
                'id'              => $fellow->pk,
                'full_name'       => $fellow->full_name,
                'username'        => $fellow->username,
                'profile_pic_url' => $fellow->profile_pic_url,
                'api'             => $this->api,
                'rankToken'       => $this->rankToken
            ];

            $this->following[] = new User($data);

        }

    }//loadFollowings

    public function getFollowers(){

        $this->loadFollowers();

        return $this->followers;
    }//getFollowers

    public function getFollowings(){

        $this->loadFollowings();

        return $this->following;
    }//getFollowings

    public function __toString()
    {
        return "ID: {$this->id} Username: {$this->username}"."\t\t\t"."Name: {$this->full_name}".PHP_EOL;
    }

}//User