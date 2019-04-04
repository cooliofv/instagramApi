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

    /** @var User */
    private $currentUser;

    /** @var string */
    private $email;

    /** @var string */
    private $phone;

    /** @var integer */
    private $gender;

    /** @var Message */
    private $messages;

    public function __construct()
    {
        $this->api = new Instagram();
        $this->rankToken = Signatures::generateUUID();
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

        return $this;
    }//login

    public function getUser()
    {
        return $this->currentUser;
    }//getUser

    public function getSomeData()
    {
        return $this->api->timeline->getTimelineFeed();
    }

    public function postPhoto($data)
    {
        $this->api->timeline->uploadPhoto($data['picture'], $data['meta']);
    }//postPhoto

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

        $this->currentUser = new User($data);
    }//loadUser

}//Account