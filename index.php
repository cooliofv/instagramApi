<?php

require __DIR__.'/init.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

use core\Account;
use debug\Debug;

try{

    $config = require __DIR__.'/config/config.php';

    $account = Account::run()->login($config['login'], $config['password']);

  foreach ($account->getUser()->getFollowers() as $follower){
      echo $follower;
  }

    //echo $account->getSomeData() . PHP_EOL;

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


