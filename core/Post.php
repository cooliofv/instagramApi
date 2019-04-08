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

    public function __toString()
    {
        return (string)printf("%-25d%-40s%s%s",$this->id,$this->pk,$this->pictures,$this->caption);
    }

}//Post