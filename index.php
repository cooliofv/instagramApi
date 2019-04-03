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


  foreach ($account->getUser()->followers as $follower){
      echo $follower;
  }

//  echo $account->getSomeData() . PHP_EOL;



//    $items = $info->getItems();
//
//    $first = $items[0]->getMedia();
//
//    echo $first->getUser()->printJson();



}catch (Exception $exception){

    echo $exception->getMessage();
}


