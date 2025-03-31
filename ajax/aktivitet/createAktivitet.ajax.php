<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;


$handleCall = new HandleAPICall(['navn', 'sted'], ['beskrivelse'], ['GET', 'POST'], false);

$navn = $handleCall->getArgument('navn');
$sted = $handleCall->getArgument('sted');
$beskrivelse = $handleCall->getOptionalArgument('beskrivelse') ?? '';

try{
    $arrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}


// Create aktivitet
$aktivitet = Write::createAktivitet($navn, $sted, $beskrivelse, $arrangement->getId());


$handleCall->sendToClient($aktivitet->getArrObj());