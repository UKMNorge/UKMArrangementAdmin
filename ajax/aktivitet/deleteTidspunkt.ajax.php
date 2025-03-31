<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;


$handleCall = new HandleAPICall(['tidspunktId'], [], ['POST'], false);

$tidspunktId = $handleCall->getArgument('tidspunktId');

$tidspunkt = null;
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
    var_dump($e->getMessage());
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$res = Write::deleteAktivitetTidspunkt($tidspunkt);


$handleCall->sendToClient(['completed' => $res]);