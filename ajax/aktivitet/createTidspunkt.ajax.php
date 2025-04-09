<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetKlokkeslett;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$requiredArguments = [  
    'maksAntall', 
    'aktivitetId',
    'harPaamelding', 
    'erSammeStedSomAktivitet'
];

$optionalArguments = [
    'start',
    'slutt',
    'hendelseId',
    'sted',
    'klokkeslettId',
];

$handleCall = new HandleAPICall($requiredArguments, $optionalArguments, ['POST'], false);

$klokkeslettId = $handleCall->getOptionalArgument('klokkeslettId') ?? null;

$startDateTime = null;
$slutDateTime = null;
$klokkeslett = null;

if($klokkeslettId != null) {
    $klokkeslett = AktivitetKlokkeslett::getById($klokkeslettId);
    
    // Define start og slutt fra klokkeslett
    $startDateTime = $klokkeslett->getStart();
    $slutDateTime = $klokkeslett->getStop();
} else {
    $startArg = $handleCall->getOptionalArgument('start') ?? null;
    $slutArg = $handleCall->getOptionalArgument('slutt') ?? null;
    
    // Hvis ingen klokkeslett, da må datoer sendes
    if($startArg == null || $slutArg == null) {
        $handleCall->sendErrorToClient('Dato er ikke gyldig', 401);
    }

    $startDateTime = new DateTime($startArg);
    $slutDateTime = new DateTime($slutArg);
}


$maksAntall = (int)$handleCall->getArgument('maksAntall') ?? 0;
$aktivitetId = $handleCall->getArgument('aktivitetId');
$harPaamelding = $handleCall->getArgument('harPaamelding') == 'true' ?? false;
$erSammeStedSomAktivitet = $handleCall->getArgument('erSammeStedSomAktivitet') == 'true' ?? false;

$sted = $handleCall->getOptionalArgument('sted') ?? ' ';
$hendelseId = $handleCall->getOptionalArgument('hendelseId') ?? null;

if(!$erSammeStedSomAktivitet && strlen(trim($sted)) < 1) {
    $handleCall->sendErrorToClient('Argumentet sted er ugyldig!', 400);
    die;
}


try{
    $aktivitet = new Aktivitet($aktivitetId);
    $tryintAccessPlId = $aktivitet->getPlId();

    $arrangement = new Arrangement(get_option('pl_id'));

    if($tryintAccessPlId != $arrangement->getId()) {
        $handleCall->sendErrorToClient('Du har ikke tilgang til denne aktiviteten', 401);
    }

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$aktivitetTidspunkt = Write::createAktivitetTidspunkt(
    $sted, 
    $startDateTime, 
    $slutDateTime, 
    null, 
    $maksAntall, 
    $aktivitetId, 
    $hendelseId, 
    $harPaamelding,
    $erSammeStedSomAktivitet
);


$handleCall->sendToClient($aktivitetTidspunkt->getArrObj());