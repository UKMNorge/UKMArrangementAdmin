<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$requiredArguments = [
    'tidspunktId',
    'start',
    'slutt',
    'maksAntall', 
    'aktivitetId',
    'harPaamelding', 
    'erSammeStedSomAktivitet',
    'kunInterne',
];

$optionalArguments = [
    'hendelseId',
    'sted',
];

$handleCall = new HandleAPICall($requiredArguments, $optionalArguments, ['POST'], false);


$tidspunktId = $handleCall->getArgument('tidspunktId');

$startDateTime = new DateTime($handleCall->getArgument('start'));
$slutDateTime = new DateTime($handleCall->getArgument('slutt'));
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
    $hendelseId,
    $harPaamelding,
    $erSammeStedSomAktivitet,
    $kunInterne,
);


$handleCall->sendToClient($aktTidspunkt->getArrObj());