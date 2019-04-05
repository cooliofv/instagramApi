<?php

require __DIR__.'/init.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

use core\Account;
use debug\Debug;

try{

    $config = require __DIR__.'/config/config.php';

    $account = new Account($config['login'], $config['password']);

//
//    echo $account->getSomeData();
//    exit;

//    $id = $account->user->getFollowings()[0]->getId();


    $account->paginateFeed(20);
    $account->paginateFeed(40);
    $account->paginateFeed(80);


    echo count((array)$account->feed);
    foreach ($account->feed as $post){

      echo "<div><p>{$post->taken_at}</p>";
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


