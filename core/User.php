<?php


namespace core;


class User{

    public $id;
    public $name;
    public $username;
    public $website;
    public $bio;

    public $posts;
    public $followers;
    public $following;


    public function __construct($id, $name, $username, $website, $bio, $posts = null, $followers = null, $following = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->website = $website;
        $this->bio = $bio;
        $this->posts = $posts;
        $this->followers = $followers;
        $this->following = $following;

    }

    public function __toString()
    {
        return "ID: {$this->id} Name: {$this->name}".PHP_EOL;
    }

}//User