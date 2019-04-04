<?php

//function LoadClass($class){
//
//    if(file_exists("{$class}.php")){
//
//        require_once "{$class}.php";
//    }
//
//}
//
//spl_autoload_register('LoadClass');

require __DIR__.'/vendor/autoload.php';

require_once './core/Account.php';
require_once './core/User.php';

use core\Account;


try{

  $account = Account::run()->login('cooliofv', 'QxBQ392c');

//  echo var_dump($account->getUser()->following);


//  foreach ($account->getUser()->getFollowers() as $follower){
//      echo $follower;
//  }

//  echo $account->getSomeData() . PHP_EOL;

    $pic = __DIR__.'/pictures/download.jpeg';

    $metadata = [
        'caption' => 'Cool post from API'
    ];

    $data = [

        'picture' => $pic,
        'meta' => $metadata
    ];

    $account->PostPhoto($data);

}catch (Exception $exception){

    echo $exception->getMessage();
}


