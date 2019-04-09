<?php


namespace core;

use debug\Debug;
use InstagramAPI\Instagram;
use InstagramAPI\Signatures;

class Account
{
    /** @var Instagram */
    private $api;

    /** @var mixed|string */
    private $rankToken;

    /** @var Message */
    private $messages;

    /** @var string  */
    private $maxFeedId = null;

    /** @var Post */
    public $feed;

    /** @var User */
    public $user;

    /** Account constructor. */
    public function __construct($login, $password)
    {
        $this->api = new Instagram();
        $this->rankToken = Signatures::generateUUID();

        $this->api->login($login, $password);
        $this->user = $this->loadUser($login);
    }

    public function getUserInfo($id)
    {
        $result = $this->api->people->getFriendship($id);

        Debug::prn($result);
        exit;

    }

    /** @param $data */
    public function postPhoto($data)
    {
        $this->api->timeline->uploadPhoto($data['picture'], $data['meta']);
    }//postPhoto

    /** @param $feedCount integer  */
    public function paginateFeed($feedCount = 0)
    {
        while(count((array)$this->feed) < $feedCount ){
            $this->loadFeed();
            if(!isset($this->maxFeedId))
                break;
        }//while
    }//paginateFeed

    /** @param  $userId integer
     *  @return object
     */
    public function followUser($userId)
    {
        $result = $this->api->people->follow($userId);

        return json_decode($result);
    }//followUser

    /** @param  $userId integer
     *  @return object
     */
    public function unfollowUser($userId)
    {
        $result = $this->api->people->unfollow($userId);

        return json_decode($result);
    }//unfollowUser

    /** @var $counter integer
     *  @var $reverse boolean
     */
    public function massUnfollowing($counter = 50, $reverse = false)
    {
        $followings = $this->user->getFollowings();

        $arrayLength = count($followings);

        if($arrayLength < $counter)
            $counter = $arrayLength;

        for ($i = 0; $i < $counter; $i++){

           if($reverse){
               $user = array_pop($followings);
           }else{
               $user = array_shift($followings);
           }//else

            $this->unfollowUser($user->getId());

           sleep(2);

        }//for counter
    }//massUnfollowing

    /** @param  $postId string
     *  @return object
     */
    public function likePost($postId)
    {
        $result = $this->api->media->like($postId);

        return json_decode($result);
    }//likePost

    /** @param  $postId string
     *  @return object
     */
    public function unlikePost($postId)
    {
        $result = $this->api->media->unlike($postId);

        return json_decode($result);
    }//unlikePost

    /** @param  $name string
     *  @return object array
     */
    public function getFollowersByName($name)
    {
        $resultUser = $this->loadUser($name);

        $followers = $resultUser->getFollowers();

        return $followers;
    }//getFollowersByName

    /** @return  array of Users */
    public function getDontFollowBack(){

        $dontFollowBack = [];

        foreach ($this->user->getFollowings() as $following){

            $result = $this->api->people->getFriendship($following->getId());
            $result = json_decode($result);

            if(!$result->followed_by){
                $dontFollowBack[] = $following;
            }
        }//foreach

        return $dontFollowBack;
    }//getDontFollowBack

    /** @return void */
    private function loadFeed()
    {
        $feedPosts = $this->api->timeline->getTimelineFeed($this->maxFeedId);

        $this->maxFeedId = $feedPosts->getNextMaxId();

        if($this->maxFeedId === null)
            return;

        $feedPosts = json_decode($feedPosts);

//        sleep(random_int(3,7));

        foreach ($feedPosts->feed_items as $feed_item) {

            if(!isset($feed_item->media_or_ad))
                continue;

            $thumbnails = [];
            $pictures = [];

            if(isset($feed_item->media_or_ad->carousel_media)){

                foreach ($feed_item->media_or_ad->carousel_media as $media){

                    $thumbnails[] = $media->image_versions2->candidates[1]->url;
                    $pictures[] = $media->image_versions2->candidates[0]->url;
                }//foreach
            }else{
                $thumbnails[] = $feed_item->media_or_ad->image_versions2->candidates[1]->url;
                $pictures[] = $feed_item->media_or_ad->image_versions2->candidates[0]->url;
            }//else

            $data = [

                'id'         => $feed_item->media_or_ad->id,
                'pk'         => $feed_item->media_or_ad->pk,
                'thumbnails' => $thumbnails,
                'pictures'   => $pictures,
                'caption'    => isset($feed_item->media_or_ad->caption) ? $feed_item->media_or_ad->caption->text : '',
                'taken_at'   => $feed_item->media_or_ad->taken_at,
                'likes'      => null,
                'comments'   => null
            ];

            $this->feed[] = new Post($data);
        }//foreach
    }//loadFeed

    /**
     * @param $name string
     * @return User
     */
    private function loadUser($name)
    {
        $userData = $this->api->people->getInfoByName($name);
        $data = json_decode($userData);

        $data = [
            'id'              => $data->user->pk,
            'full_name'       => $data->user->full_name,
            'username'        => $data->user->username,
            'profile_pic_url' => $data->user->profile_pic_url,
            'api'             => $this->api,
            'rankToken'       => $this->rankToken,
            'website'         => $data->user->external_url,
            'biography'       => $data->user->biography,
            'follower_count'  => $data->user->follower_count,
            'following_count' => $data->user->following_count,
            'media_count'     => $data->user->media_count
        ];

        return new User($data);
    }//loadUser

}//Account