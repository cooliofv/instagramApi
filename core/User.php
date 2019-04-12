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

    /** @var User */
    private $followers;

    /** @var User*/
    private $following;

    /** @var integer */
    private $follower_count;

    /** @var integer */
    private $following_count;

    /** @var string */
    private $maxFollowerId;

    /** @var string */
    private $maxFollowingId;

    /** @var Post */
    public $posts;

    /** @var string  */
    private $maxPostId = null;

    /** @var integer */
    private $media_count;

    private $biography;

    /** @var Instagram */
    private $api;

    /** @var string */
    private $rankToken;

    /** @var bool */
    private $is_private;

    /**
     * User constructor.
     * @param $data array associative array with correct keys to assign class fields
     */
    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = isset($data[$key]) ? $data[$key] : '';
        }//foreach
    }//__constructor

    /** @return int */
    public function getId(): int
    {
        return $this->id;
    }//getId

    public function echoInfo(){

        echo "<div><p>ID: {$this->id}</p>";
        echo "<div><p>Name: {$this->full_name}</p>";
        echo "<img src = '{$this->profile_pic_url}' alt='avatar'>";
        echo "</div>";

    }//echoInfo

    /** @param $userId integer
     *  @param $postCount integer
     * @return array of Post
     */
    public function getPosts($userId,$postCount = 20)
    {
        while (count((array)$this->posts) < $postCount){
            $posts = $this->loadPosts($userId, $this->maxPostId);

            $result = array_merge((array)$this->posts, $posts);
            $this->posts = $result;

            if(!isset($this->maxPostId))
                break;
        }//while

        return $this->posts;
    }//paginatePosts

    /** @return array of User */
    public function getFollowers()
    {
        while(count((array)$this->followers) < $this->follower_count) {

            $followers = $this->loadFollowers($this->maxFollowerId);

            sleep(rand(5,8));//Pause to prevent API throttling

            $result = array_merge((array)$this->followers, $followers);
            $this->followers = $result;

            if(!isset($this->maxFollowerId))
                break;
        }//while

        return $this->followers;
    }//getFollowers

    /** @return array of User */
    public function getFollowings()
    {
        while(count((array)$this->following) < $this->following_count) {

            $following = $this->loadFollowings($this->maxFollowingId);

            sleep(rand(5,8));//Pause to prevent API throttling

            $result = array_merge((array)$this->following, $following);
            $this->following = $result;

            if(!isset($this->maxFollowingId))
                break;
        }//while

        return $this->following;
    }//getFollowings

    /** @return boolean */
    public function isPrivate()
    {
        return $this->is_private;
    }

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
            'rankToken'       => $this->rankToken,
            'is_private'      => $obj->is_private
        ];

        return $data;
    }//objToArray

    /**
     * @param $userId integer
     * @param null $maxPostId
     * @return array of Post
     */
    private function loadPosts($userId, $maxPostId = null)
    {
        $posts = $this->api->timeline->getUserFeed($userId, $maxPostId);

        $this->maxPostId = $posts->getNextMaxId();

        if($this->maxPostId === null && count((array)$posts->getItems()) === 0)
            return [];

        $posts = json_decode($posts);
        $result = [];

        foreach ($posts->items as $post){

            $thumbnails = [];
            $pictures = [];

            if(isset($post->carousel_media)){

                foreach ($post->carousel_media as $media) {

                    $thumbnails[] = $media->image_versions2->candidates[1]->url;
                    $pictures[] = $media->image_versions2->candidates[0]->url;
                }
            }else{

                $thumbnails[] = $post->image_versions2->candidates[1]->url;
                $pictures[] = $post->image_versions2->candidates[0]->url;
            }//else


            $data = [

                'id'         => $post->id,
                'pk'         => $post->pk,
                'thumbnails' => $thumbnails,
                'pictures'   => $pictures,
                'caption'    => isset($post->caption) ? $post->caption->text : '',
                'taken_at'   => $post->taken_at,
                'likes'      => null,
                'comments'   => null
            ];

            $result[] = new Post($data);
        }//foreach
        return $result;
    }//loadPosts

    private function loadFollowers($nextMaxId = null)
    {
        $followersData = $this->api->people->getFollowers($this->id, $this->rankToken, $searchStr = null, $nextMaxId);

        $this->maxFollowerId = $followersData->getNextMaxId();

        if($this->maxFollowerId === null && count((array)$followersData->getUsers()) === 0)
            return [];

        $followers = json_decode($followersData);

        $result = [];

        foreach($followers->users as $follower){

            $data = $this->objToArray($follower);

            $result[] = new User($data);
        }//foreach

        return $result;
    }//loadFollowInfo

    private function loadFollowings($nextMaxId = null)
    {
        $followingData = $this->api->people->getFollowing($this->id, $this->rankToken, $searchStr = null, $nextMaxId);

        $this->maxFollowingId = $followingData->getNextMaxId();

        if($this->maxFollowingId === null && count((array)$followingData->getUsers()) === 0)
            return [];

        $following = json_decode($followingData);

        $result = [];

        foreach ($following->users as $fellow){

            $data = $this->objToArray($fellow);

            $result[] = new User($data);
        }//foreach

        return $result;
    }//loadFollowings

}//User