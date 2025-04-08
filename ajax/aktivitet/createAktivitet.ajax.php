<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$requiredArguments = [  
    'navn', 
    'sted',
];

$optionalArguments = [
    'beskrivelse',
    'beskrivelseLeder',
];

$handleCall = new HandleAPICall($requiredArguments, $optionalArguments, ['POST'], false);


$navn = $handleCall->getArgument('navn');
$sted = $handleCall->getArgument('sted');

$beskrivelse = $handleCall->getOptionalArgument('beskrivelse') ?? ' ';
$beskrivelseLeder = $handleCall->getOptionalArgument('beskrivelseLeder') ?? ' ';


try{
    $arrangement = new Arrangement(get_option('pl_id'));
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$aktivitet = Write::createAktivitet($navn, $sted, $beskrivelse, $beskrivelseLeder, $arrangement->getId());


$handleCall->sendToClient($aktivitet->getArrObj());