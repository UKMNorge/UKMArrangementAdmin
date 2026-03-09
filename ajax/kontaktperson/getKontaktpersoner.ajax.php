<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall([],[], ['GET', 'POST'], false);

$arrangement = null;
$retKontaktpersoner = [];

try{
    $arrangement = new Arrangement(get_option('pl_id'));
    $kontaktpersoner = $arrangement->getKontaktpersoner()->getAll();

    foreach($kontaktpersoner as $kontaktperson) {
        $retKontaktpersoner[] = $kontaktperson->getArray();
    }

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$handleCall->sendToClient($retKontaktpersoner);
