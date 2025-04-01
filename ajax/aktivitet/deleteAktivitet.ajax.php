<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;


$handleCall = new HandleAPICall(['aktivitetId'], [], ['POST'], false);

$aktivitetId = $handleCall->getArgument('aktivitetId');

$aktivitet = null;
try{
    $aktivitet = new Aktivitet($aktivitetId);
    $tryintAccessPlId = $aktivitet->getPlId();

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

// Slett alle tidspunkter først
foreach($aktivitet->getTidspunkter()->getAll() as $tidspunkt) {
    Write::deleteAktivitetTidspunkt($tidspunkt);
}

$res = Write::deleteAktivitet($aktivitet);

$handleCall->sendToClient(['completed' => $res == true]);