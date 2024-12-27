<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Geografi\Fylker;
use UKMNorge\OAuth2\HandleAPICall;


$handleCall = new HandleAPICall([],[], ['GET', 'POST'], false);


$retArr = [];

foreach(Fylker::getAll() as $fylke) {
    $retArr[] = array(
        'fylkeName' => $fylke->getNavn(),
        'fylkeId' => $fylke->getId(),
    );
}

$handleCall->sendToClient($retArr);