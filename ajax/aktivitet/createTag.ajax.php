<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$requiredArguments = [  
    'navn', 
];

$optionalArguments = [
    'beskrivelse',
];

$handleCall = new HandleAPICall($requiredArguments, $optionalArguments, ['POST'], false);


$navn = $handleCall->getArgument('navn');

$beskrivelse = $handleCall->getOptionalArgument('beskrivelse') ?? ' ';

$arrangement = null;
try{
    $arrangement = new Arrangement(get_option('pl_id'));
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$tag = Write::createTag($navn, $beskrivelse, $arrangement->getId());


$handleCall->sendToClient($tag->getArrObj());