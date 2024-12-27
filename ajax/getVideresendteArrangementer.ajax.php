<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall([],[], ['GET', 'POST'], false);

$arrangement = null;

try{
    $arrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$retArr = [];

foreach($arrangement->getVideresending()->getAvsendere() as $arrangAvsender) {
    $arrang = $arrangAvsender->getArrangement();
    $fylke = $arrang->getFylke();

    $retArr[] = array(
        'fylkeName' => $fylke->getNavn(),
        'fylkeId' => $fylke->getId(), 
        'arrangementName' => $arrang->getNavn(),
        'festivalId' => $arrang->getId(),
        'link' => $arrang->getLink()
    );
}

$handleCall->sendToClient($retArr);