<?php

require_once('UKM/Autoloader.php');


use UKMNorge\Arrangement\Program\HendelseGruppe;
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

$hendelseGrupper = HendelseGruppe::getAlleByArrangement($arrangement);

$retArr = [];

foreach($hendelseGrupper as $gruppe) {
    $gruppe->getHendelser();

    $obj = [
        'id' => $gruppe->getId(),
        'navn' => $gruppe->getNavn(),
        'beskrivelse' => $gruppe->getBeskrivelse(),
        'tag' => $gruppe->getTag(),
        'hendelser' => $gruppe->getHendelser(),
    ];

    $retArr[] = $obj;
}

$handleCall->sendToClient($retArr);