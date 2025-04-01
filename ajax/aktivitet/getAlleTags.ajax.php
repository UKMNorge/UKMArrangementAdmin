<?php

use UKMNorge\Arrangement\Aktivitet\AktivitetTag;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall([],[], ['GET', 'POST'], false);

try{
    $arrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}
$retArr = [];

foreach(AktivitetTag::getAllByArrangement($arrangement->getId()) as $tag) {
    $retArr[] = $tag->getArrObj();
}


$handleCall->sendToClient($retArr);