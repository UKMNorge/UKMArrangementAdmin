<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall([],[], ['GET', 'POST'], false);

try{
    $arrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

// Create aktivitet
// $aktivitet = Write::createAktivitet('Testaktivitet', 'Sirius sted', 'Beskrivelse YoYo', 123);


// Create activitetTidspunkt
$start = new DateTime();
$varighetMinutter = 60;
$maxAntall = 10;
// $aktivitetTidspunkt = Write::createAktivitetTidspunkt('TDS med deltaker', $start, $varighetMinutter, $maxAntall, 1, null);


// Create aktivitetDeltaker
// $aktivitetDeltaker = Write::createAktivitetDeltaker('46516256');


// Add deltaker to aktivitetsTidspunkt
// $deltakerTidspunkt = Write::addDeltakerToTidspunkt($aktivitetDeltaker->getId(), 30, 'wWW');

$retArr = [];


foreach(Aktivitet::getAllByArrangement($arrangement->getId()) as $aktivitet) {
    $retArr[] = $aktivitet->getArrObj();
}


$handleCall->sendToClient($retArr);