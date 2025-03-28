<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$requiredArguments = [
    'aktivitetId',
    'navn',
    'sted',
    'beskrivelse',
];

$handleCall = new HandleAPICall($requiredArguments, [], ['POST'], false);


$aktivitetId = $handleCall->getArgument('aktivitetId');
$navn = $handleCall->getArgument('navn');
$sted = $handleCall->getArgument('sted');
$beskrivelse = $handleCall->getArgument('beskrivelse');

try{
    $aktivitet = new Aktivitet($aktivitetId);
    $tryintAccessPlId = $aktivitet->getPlId();

    $arrangement = new Arrangement(get_option('pl_id'));

    if($tryintAccessPlId != $arrangement->getId()) {
        $handleCall->sendErrorToClient('Du har ikke tilgang til dette arrangementet', 401);
    }

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$aktTidspunkt = Write::updateAktivitet(
    $aktivitetId,
    $navn,
    $sted,
    $beskrivelse,
);


$handleCall->sendToClient($aktTidspunkt->getArrObj());