<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$requiredArguments = [  
    'fra', 
    'til',
];

$handleCall = new HandleAPICall($requiredArguments, [], ['POST'], false);

$startDateTime = new DateTime($handleCall->getArgument('fra'));
$stopDateTime = new DateTime($handleCall->getArgument('til'));


$arrangement = null;
try{
    $arrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$aktivitetKlokkeslett = Write::createAktivitetKlokkeslett(
    $startDateTime, 
    $stopDateTime,
    $arrangement->getId()
);


$handleCall->sendToClient($aktivitetKlokkeslett->getArrObj());