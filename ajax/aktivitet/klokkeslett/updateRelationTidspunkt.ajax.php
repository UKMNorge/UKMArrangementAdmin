<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetKlokkeslett;
use UKMNorge\Arrangement\Aktivitet\AktivitetTag;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;


$handleCall = new HandleAPICall(['tidspunktId'], ['klokkeslettId'], ['POST'], false);


$tidspunktId = $handleCall->getArgument('tidspunktId');
$klokkeslettId = $handleCall->getOptionalArgument('klokkeslettId') ?? null;

$tidspunkt = null;
try{
    $tidspunkt = new AktivitetTidspunkt($tidspunktId);
    $tryintAccessPlId = $tidspunkt->getAktivitet()->getPlId();

    $arrangement = new Arrangement(get_option('pl_id'));

    if($tryintAccessPlId != $arrangement->getId()) {
        $handleCall->sendErrorToClient(['errorMessage' => 'Du har ikke tilgang til dette arrangementet'], 401);
    }

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$kSlett = null;
if($klokkeslettId != null) {
    try {
        $kSlett = AktivitetKlokkeslett::getById($klokkeslettId);
    } catch(Exception $e) {
        $handleCall->sendErrorToClient('Kunne ikke hente klokkeslett', 401);
    }
}


// Metoden fjerner gammelt klokkeslett og legger til nytt klokkeslett
$res = Write::addKlokkeslettToTidspunkt($tidspunkt, $kSlett);


$handleCall->sendToClient(['completed' => $res]);