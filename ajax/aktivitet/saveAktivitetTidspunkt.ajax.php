<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$requiredArguments = [
    'tidspunktId', 
    'sted', 
    'start', 
    'slutt',
    'varighet', 
    'maksAntall', 
    'aktivitetId',
    'harPaamelding', 
    'erSammeStedSomAktivitet'
];

$optionalArguments = [
    'hendelseId', 
];

$handleCall = new HandleAPICall($requiredArguments, $optionalArguments, ['POST'], false);


$tidspunktId = $handleCall->getArgument('tidspunktId');
$sted = $handleCall->getArgument('sted');
$startDateTime = new DateTime($handleCall->getArgument('start'));
$slutDateTime = new DateTime($handleCall->getArgument('slutDateTime'));
$varighet = $handleCall->getArgument('varighet');
$maksAntall = $handleCall->getArgument('maksAntall');
$aktivitetId = $handleCall->getArgument('aktivitetId');
$hendelseId = $handleCall->getOptionalArgument('hendelseId') ?? null;
$harPaamelding = $handleCall->getArgument('harPaamelding') == 'true' ?? false;
$erSammeStedSomAktivitet = $handleCall->getArgument('erSammeStedSomAktivitet') == 'true' ?? false;

try{
    $tidspunkt = new AktivitetTidspunkt($tidspunktId);
    $tryintAccessPlId = $tidspunkt->getAktivitet()->getPlId();

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

$aktTidspunkt = Write::updateAktivitetTidspunkt(
    $tidspunktId,
    $sted,
    $startDateTime,
    $slutDateTime,
    $varighet,
    $maksAntall,
    $hendelseId,
    $harPaamelding,
    $erSammeStedSomAktivitet,
);


$handleCall->sendToClient($aktTidspunkt->getArrObj());