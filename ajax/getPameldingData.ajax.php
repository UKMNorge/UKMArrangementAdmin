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

$retArr = [
    'pameldte' => [],
    'videresending' => []
];

foreach($arrangement->getInnslag()->getAll() as $innslag) {
    $retArr['pameldte'][] = [
        'innslagId' => $innslag->getId(),
        'innslagNavn' => $innslag->getNavn(),
        'innslagType' => $innslag->getType()->getNavn(),
        'personer' => $innslag->getPersoner()->getAll()
    ];
}

foreach($arrangement->getVideresending()->getAvsendere() as $avsender) {
    $fra = $avsender->getArrangement();

    foreach($fra->getVideresendte($arrangement->getId())->getAll() as $innslag) {
        $retArr['videresending'][] = [
            'fra' => $fra->getNavn(),
            'innslagId' => $innslag->getId(),
            'innslagNavn' => $innslag->getNavn(),
            'innslagType' => $innslag->getType()->getNavn(),
            'personer' => $innslag->getPersoner()->getAll()
        ];
    }
}


$handleCall->sendToClient($retArr);