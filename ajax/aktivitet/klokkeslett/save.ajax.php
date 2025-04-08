<?php

use UKMNorge\Arrangement\Aktivitet\AktivitetKlokkeslett;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$requiredArguments = [
    'id',
    'fra',
    'til',
];


$handleCall = new HandleAPICall($requiredArguments, [], ['POST'], false);

$id = (int)$handleCall->getArgument('id');

$fraDateTime = new DateTime($handleCall->getArgument('fra'));
$tilDateTime = new DateTime($handleCall->getArgument('til'));

$arrangement = null;
try{
    $klokkeSlett = AktivitetKlokkeslett::getById($id);

    $tryintAccessPlId = $klokkeSlett->getPlId();

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

$kSlett = Write::updateAktivitetKlokkeslett(
    $id,
    $fraDateTime,
    $tilDateTime,
    $arrangement->getId()
);


$handleCall->sendToClient($kSlett->getArrObj());