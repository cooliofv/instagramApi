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

    /** @var string */
    private $email;

    /** @var string */
    private $phone;

    /** @var integer */
    private $gender;

    /** @var Message */
    private $messages;

    private $maxId;

    /** @var Post */
    public $feed;

    /** @var Post */
    public $posts;

    /** @var User */
    public $user;

    /** Account constructor. */
    public function __construct()
    {

        $this->api = new Instagram();
        $this->rankToken = Signatures::generateUUID();
        $this->maxId = null;
    }

    public static function run()
    {
        return new self;
    }//run

    /**
     * @param $login string
     * @param $password string
     * @return $this Account
     */
    public function login($login, $password)
    {
        $this->api->login($login, $password);
        $this->loadUser();
//        $this->loadFeed();
        $this->posts = $this->getUserPosts($this->user->getId());

        return $this;
    }//login

    public function getSomeData()
    {
        return $this->api->timeline->getTimelineFeed();
    }

    public function postPhoto($data)
    {
        $this->api->timeline->uploadPhoto($data['picture'], $data['meta']);
    }//postPhoto

    public function getUserPosts($userId, $maxId = null){

        $posts = $this->api->timeline->getUserFeed($userId, $maxId);

        $this->maxId = $posts->getNextMaxId();

        $posts = json_decode($posts);

        $resultPosts = [];

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
                'caption'    => $post->caption->text,
                'taken_at'   => $post->taken_at,
                'likes'      => null,
                'comments'   => null
            ];

            $resultPosts[] = new Post($data);

        }//foreach

        return $resultPosts;
    }//loadPosts

    public function paginateFeed()
    {

            $this->loadFeed();


    }//paginateFeed

    private function loadFeed()
    {
        $feedPosts = $this->api->timeline->getTimelineFeed($this->maxId);

        $this->maxId = $feedPosts->getNextMaxId();

//        Debug::dd($feedPosts);

        if($this->maxId === null)
            return;

        $feedPosts = json_decode($feedPosts);

//        sleep(3);

        $resultPosts = [];

        foreach ($feedPosts->feed_items as $feed_item) {

            if(!isset($feed_item->media_or_ad))
                continue;

            $thumbnails = [];
            $pictures = [];

            if(isset($feed_item->media_or_ad->carousel_media)){

                foreach ($feed_item->media_or_ad->carousel_media as $media) {

                    $thumbnails[] = $media->image_versions2->candidates[1]->url;
                    $pictures[] = $media->image_versions2->candidates[0]->url;
                }
            }else{

//                Debug::dd($feed_item);
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

    private function loadUser()
    {
        $userData = $this->api->account->getCurrentUser();
        $data = json_decode($userData);

        $this->email = $data->user->email;
        $this->phone = $data->user->phone_number;
        $this->gender = $data->user->gender;

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

        $this->user = new User($data);
    }//loadUser

}//Account