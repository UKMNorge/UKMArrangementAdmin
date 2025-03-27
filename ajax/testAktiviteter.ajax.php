<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\Write;

use UKMNorge\OAuth2\HandleAPICall;


$handleCall = new HandleAPICall([],[], ['GET', 'POST'], false);


// Create aktivitet
// $aktivitet = Write::createAktivitet('Testaktivitet', 'Sirius sted', 'Beskrivelse YoYo', 123);


// Create activitetTidspunkt
$start = new DateTime();
$varighetMinutter = 60;
$maxAntall = 10;
$aktivitetTidspunkt = Write::createAktivitetTidspunkt('TDS med deltaker', $start, $varighetMinutter, $maxAntall, 1, null);


// Create aktivitetDeltaker
$aktivitetDeltaker = Write::createAktivitetDeltaker('46516256');


// Add deltaker to aktivitetsTidspunkt
$deltakerTidspunkt = Write::addDeltakerToTidspunkt($aktivitetDeltaker->getId(), 30, 'wWW');


die;
$retArr = [];


foreach(Aktivitet::getAllByArrangement(123) as $aktivitet) {
    $retArr[] = array(
        'aktivitetId' => $aktivitet->getId(),
        'aktivitetNavn' => $aktivitet->getNavn(),
        'aktivitetSted' => $aktivitet->getSted(),
        'aktivitetBeskrivelse' => $aktivitet->getBeskrivelse(),
        'aktivitetPlId' => $aktivitet->getPlId(),
    );
}



$handleCall->sendToClient($retArr);