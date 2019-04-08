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
    private $maxPostId = null;

    /** @var string  */
    private $maxFeedId = null;

    /** @var Post */
    public $feed;

    /** @var Post */
    public $posts;

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

    /** @param $userId integer
     *  @param $postCount integer
     */
    public function paginatePosts($userId,$postCount = 10)
    {
        while (count((array)$this->posts) < $postCount){
            $posts = $this->loadPosts($userId, $this->maxPostId);

            $result = array_merge((array)$this->posts, $posts);
            $this->posts = array_map("unserialize", array_unique(array_map("serialize",$result)));

            if(!isset($this->maxPostId))
                break;
        }//while
    }//paginatePosts

    /** @param  $userId integer
     *  @return string
     */
    public function followUser($userId)
    {
        $result = $this->api->people->follow($userId);

        return $result;
    }//followUser

    /** @param  $userId integer
     *  @return string
     */
    public function unfollowUser($userId)
    {
        $result = $this->api->people->unfollow($userId);

        return $result;
    }//unfollowUser

    /** @param  $postId string
     *  @return string
     */
    public function likePost($postId)
    {
        $result = $this->api->media->like($postId);

        return $result;
    }//likePost

    /** @param  $postId string
     *  @return string
     */
    public function unlikePost($postId)
    {
        $result = $this->api->media->unlike($postId);

        return $result;
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

    /**
     * @param $userId integer
     * @return object|void array
     */
    public function getPostsByUserId($userId)
    {
        $result = $this->loadPosts($userId);

        return $result;
    }

    /**
     * @param $userId integer
     * @param null $maxPostId
     * @return object|void array
     */
    private function loadPosts($userId, $maxPostId = null)
    {
        $posts = $this->api->timeline->getUserFeed($userId, $maxPostId);

        $this->maxPostId = $posts->getNextMaxId();

        if($this->maxPostId === null && count((array)$posts->getItems()) === 0)
            return;

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
            'biography'       => $data->user->biography
        ];

        return new User($data);
    }//loadUser

}//Account