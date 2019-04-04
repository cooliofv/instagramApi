<?php


namespace core;


class Post
{
    private $id;
    private $pk;

    private $thumbnail;
    private $picture;
    private $caption;

    private $likes;
    private $comments;


    private $taken_at;

    /**
     * Post constructor.
     * @param $data array associative array with correct keys to assign class fields
     */
    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = isset($data[$key]) ? $data[$key] : null;
        }//foreach
    }//__constructor

}//Post