<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Innslag\Typer\Typer;


$handleCall = new HandleAPICall([],[], ['GET', 'POST'], false);

$arrangement = null;
$alleTyper = null;

try{
    $arrangement = new Arrangement(get_option('pl_id'));
    $alleTyper = Typer::getAllTyper();

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangement eller typer', 401);
}


$retArr = [];


foreach($alleTyper as $type) {
    $retArr[] = [
        'id' => $type->getId(),
        'key' => $type->getKey(),
        'navn' => $type->getNavn(),
        'erEnkeltPerson' => $type->erEnkeltPerson(),
        'erViseFrem' => $type->erViseFrem(),
        'isSelected' => $arrangement->getInnslagTyper()->har($type) != null
    ];
}

$handleCall->sendToClient($retArr);