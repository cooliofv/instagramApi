<?php

require __DIR__.'/init.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

use core\Account;
use debug\Debug;

try{

    $config = require __DIR__.'/config/config.php';

    $account = new Account($config['login'], $config['password']);

//    $dontFollowBack = $account->getDontFollowBack();
//
//    foreach ($dontFollowBack as $user){
//        $user->echoInfo();
//    }





    $account->massUnfollowing(5,true);


//    $posts = $account->user->getPosts($id, 15);
//
//    foreach ($posts as $post){
//
//       $post->echoInfo();
//    }


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


