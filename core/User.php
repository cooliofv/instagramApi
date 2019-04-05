<?php


namespace core;

use debug\Debug;
use InstagramAPI\Instagram;

class User
{
    /** @var integer */
    private $id;

    /** @var string*/
    private $full_name;

    /** @var string */
    private $username;

    /** @var string */
    private $profile_pic_url;

    /** @var string */
    private $website;

    /** @var string*/
    private $biography;

    /** @var User */
    private $followers;

    /** @var User*/
    private $following;

    /** @var Instagram */
    private $api;

    /** @var string */
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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getFollowers()
    {
        $this->loadFollowers();

        return $this->followers;
    }//getFollowers

    public function getFollowings()
    {
        $this->loadFollowings();

        return $this->following;
    }//getFollowings

    public function __toString()
    {
        return (string)printf("%-15d%-20s%-40s\n",$this->id, $this->username, $this->full_name);
    }//toString

    /**
     * @param $obj Object from API
     * @return array
     */
    private function objToArray($obj)
    {
        $data = [
            'id'              => $obj->pk,
            'full_name'       => $obj->full_name,
            'username'        => $obj->username,
            'profile_pic_url' => $obj->profile_pic_url,
            'api'             => $this->api,
            'rankToken'       => $this->rankToken
        ];

        return $data;
    }//objToArray

    private function loadFollowers()
    {
        $followersData = $this->api->people->getFollowers($this->id, $this->rankToken);

        $followers = json_decode($followersData);

        foreach($followers->users as $follower){

            $data = $this->objToArray($follower);

            $this->followers[] = new User($data);
        }//foreach
    }//loadFollowInfo

    private function loadFollowings()
    {
        $followingData = $this->api->people->getFollowing($this->id, $this->rankToken);

        $following = json_decode($followingData);

        foreach ($following->users as $fellow){

            $data = $this->objToArray($fellow);

            $this->following[] = new User($data);

        }//foreach
    }//loadFollowings

}//User