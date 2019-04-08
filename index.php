<?php

require __DIR__.'/init.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

use core\Account;
use debug\Debug;

try{

    $config = require __DIR__.'/config/config.php';

    $account = new Account($config['login'], $config['password']);


    $id = $account->user->getFollowers()[7]->getId();

    $posts = $account->getPostsByUserId($id);


    foreach ($posts as $post){

        $date = date('d-m-Y', $post->taken_at);

      echo "<div><p>TIMESTAMP: {$date} </p><p>ID: {$post->pk}</p>";
      foreach ($post->thumbnails as $pic) {

          echo "<img src = '{$pic}'>";
      }
       echo "<p>{$post->caption}</p></div>";
    }


    //------POST TEST UPLOADING-------
    $pic = __DIR__.'/pictures/download.jpeg';
    $metadata = [
        'caption' => 'Cool post from API'
    ];
    $data = [

        'picture' => $pic,
        'meta' => $metadata
    ];
    //$account->postPhoto($data);

    //------POST TEST UPLOADING-------

}catch (Exception $exception){

    echo $exception->getMessage();
}


