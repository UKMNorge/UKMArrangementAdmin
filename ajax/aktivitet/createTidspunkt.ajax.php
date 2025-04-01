<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$requiredArguments = [  
    'start', 
    'slutt',
    'maksAntall', 
    'aktivitetId',
    'harPaamelding', 
    'erSammeStedSomAktivitet'
];

$optionalArguments = [
    'hendelseId',
    'sted',
];

$handleCall = new HandleAPICall($requiredArguments, $optionalArguments, ['POST'], false);

$startDateTime = new DateTime($handleCall->getArgument('start'));
$slutDateTime = new DateTime($handleCall->getArgument('slutt'));
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