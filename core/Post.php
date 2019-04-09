<?php


namespace core;

use InstagramAPI\Instagram;
use debug\Debug;

class Post
{
    public $id;
    public $pk;

    public $thumbnails;
    public $pictures;
    public $caption;

    public $likes;
    public $comments;


    public $taken_at;

    /**
     * Post constructor.
     * @param $data array associative array with correct keys to assign class fields
     */
    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = isset($data[$key]) ? $value : '';
        }//foreach
    }//__constructor

    public function echoInfo(){

        $date = date('d-m-Y', $this->taken_at);

        echo "<div><p>TIMESTAMP: {$date} </p><p>ID: {$this->pk}</p>";
        foreach ($this->thumbnails as $pic) {

            echo "<img src = '{$pic}'>";
        }
        echo "<p>{$this->caption}</p></div>";

    }//echoInfo

}//Post