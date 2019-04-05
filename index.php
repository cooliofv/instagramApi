<?php

require __DIR__.'/init.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

use core\Account;
use debug\Debug;

try{

    $config = require __DIR__.'/config/config.php';

    $account = Account::run()->login($config['login'], $config['password']);


//    echo $account->getSomeData();

  foreach ($account->getUser()->getPosts() as $post){

//      $time = new DateTime($post->taken_at);


      echo "<div><p>{$post->taken_at}</p>";
      foreach ($post->thumbnails as $pic) {

          echo "<img src = '{$pic}'>";
      }
       echo "<p>{$post->caption}</p></div>";
  }

//    var_dump($account->getUser()->getPosts()[0]);


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


