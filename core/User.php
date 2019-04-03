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
    public $followers;
    public $following;


    public function __construct($id, $name, $username, $profile_pic_url, $website = null, $bio = null, $posts = null, $followers = null, $following = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->profile_pic_url = $profile_pic_url;
        $this->website = $website;
        $this->bio = $bio;
        $this->posts = $posts;
        $this->followers = $followers;
        $this->following = $following;

    }


    public function __toString()
    {
        return "ID: {$this->id} Username: {$this->username} Name: {$this->name}".PHP_EOL;
    }

}//User