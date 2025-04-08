<?php

use UKMNorge\Arrangement\Aktivitet\AktivitetKlokkeslett;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;


$handleCall = new HandleAPICall(['id'], [], ['POST'], false);

$id = $handleCall->getArgument('id');

$klokkeslett = null;
try{
    $klokkeslett = AktivitetKlokkeslett::getById($id);

    $tryintAccessPlId = $klokkeslett->getPlId();

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


$res = Write::deleteAktivitetKlokkeslett($klokkeslett);

$handleCall->sendToClient(['completed' => $res == true]);