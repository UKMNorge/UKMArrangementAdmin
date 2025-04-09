<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Aktivitet\AktivitetKlokkeslett;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$requiredArguments = [
    'tidspunktId',
    'maksAntall', 
    'aktivitetId',
    'harPaamelding', 
    'erSammeStedSomAktivitet',
    'kunInterne',
];

$optionalArguments = [
    'hendelseId',
    'sted',
    'start',
    'slutt',
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


$tidspunktId = $handleCall->getArgument('tidspunktId');

// $varighet = $handleCall->getArgument('varighet');
$maksAntall = $handleCall->getArgument('maksAntall');
$aktivitetId = $handleCall->getArgument('aktivitetId');
$harPaamelding = $handleCall->getArgument('harPaamelding') == 'true' ?? false;
$erSammeStedSomAktivitet = $handleCall->getArgument('erSammeStedSomAktivitet') == 'true' ?? false;
$kunInterne = $handleCall->getArgument('kunInterne') == 'true' ?? false;


$sted = $handleCall->getOptionalArgument('sted') ?? ' ';
$hendelseId = $handleCall->getOptionalArgument('hendelseId') ?? null;

if(!$erSammeStedSomAktivitet && strlen(trim($sted)) < 1) {
    $handleCall->sendErrorToClient(['errorMessage' => 'Argumentet sted er ugyldig!'], 400);
    die;
}

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

$aktTidspunkt = Write::updateAktivitetTidspunkt(
    $tidspunktId,
    $sted,
    $startDateTime,
    $slutDateTime,
    null,
    $maksAntall,
    is_array($hendelseId) ? $hendelseId['id'] : $hendelseId,
    $harPaamelding,
    $erSammeStedSomAktivitet,
    $kunInterne,
);


$handleCall->sendToClient($aktTidspunkt->getArrObj());