<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;


$imageFile = $_FILES['imageFile'] ?? null;

$handleCall = new HandleAPICall(['aktivitetId'], [], ['POST'], false);
$aktivitetId = $handleCall->getArgument('aktivitetId');


$aktivitet = null;
try{
    $aktivitet = new Aktivitet($aktivitetId);
    $tryintAccessPlId = $aktivitet->getPlId();
    
    $arrangement = new Arrangement(get_option('pl_id'));
    
    if($tryintAccessPlId != $arrangement->getId()) {
        $handleCall->sendErrorToClient('Du har ikke tilgang til denne aktiviteten', 401);
    }

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$oldUrl = $aktivitet->getImage();

try {
    $uploadRes = Write::uploadAktivitetImage($imageFile, $aktivitet, $imageFile == null);
    
    $handleCall->sendToClient(['imageUrl' => $uploadRes, 'oldUrl' => $oldUrl, 'isDeleted' => $imageFile == null]);
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke laste opp bildet: '. $e->getMessage(), $e->getCode());
}

