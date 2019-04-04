<?php


namespace core;


class User{

    public $id;
    public $name;
    public $username;
    public $profile_pic_url;
    public $website;
    public $bio;
    public $posts;
    private $followers;
    private $following;

    private $api;
    private $rankToken;


    public function __construct($id, $name, $username, $profile_pic_url, $api, $rankToken, $website = null, $bio = null, $posts = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->profile_pic_url = $profile_pic_url;
        $this->api = $api;
        $this->rankToken = $rankToken;
        $this->website = $website;
        $this->bio = $bio;
        $this->posts = $posts;
    }

    private function loadFollowers(){

        $followersData = $this->api->people->getFollowers($this->id, $this->rankToken);

        $followers = json_decode($followersData);

        foreach($followers->users as $follower){

            $this->followers[] = new User(
                $follower->pk,
                $follower->full_name,
                $follower->username,
                $follower->profile_pic_url,
                $this->api,
                $this->rankToken
            );

        }

    }//loadFollowInfo

    private function loadFollowings(){

        $followingData = $this->api->people->getFollowing($this->id, $this->rankToken);

        $following = json_decode($followingData);

        foreach ($following->users as $fellow){

            $this->following[] = new User(
                $fellow->pk,
                $fellow->full_name,
                $fellow->username,
                $fellow->profile_pic_url,
                $this->api,
                $this->rankToken
            );

        }

    }//loadFollowings

    public function getFollowers(){

        $this->loadFollowers();

        return $this->followers;
    }//getFollowers

    public function getFollowings(){

        $this->loadFollowings();

        return $this->following;
    }

    public function __toString()
    {
        return "ID: {$this->id} Username: {$this->username} Name: {$this->name}".PHP_EOL;
    }

}//User