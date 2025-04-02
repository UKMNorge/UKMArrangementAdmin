<?php

use UKMNorge\Arrangement\Aktivitet\AktivitetTag;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$requiredArguments = [
    'id',
    'navn',
];

$handleCall = new HandleAPICall($requiredArguments, ['beskrivelse'], ['POST'], false);


$id = (int)$handleCall->getArgument('id');
$navn = $handleCall->getArgument('navn');
$beskrivelse = $handleCall->getOptionalArgument('beskrivelse') ?? '';

$arrangement = null;
try{
    $tag = AktivitetTag::getById($id);

    $tryintAccessPlId = $tag->getPlId();

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

$tag = Write::updateAktivitetTag(
    $id,
    $navn,
    $beskrivelse,
    $arrangement->getId()
);


$handleCall->sendToClient($tag->getArrObj());