<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall([],[], ['GET', 'POST'], false);

$arrangement = null;
try{
    $arrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}
$retArr = [];

foreach($arrangement->getProgram()->getAbsoluteAll() as $hendelse) {
    $retArr[] = $hendelse;
}


$handleCall->sendToClient($retArr);