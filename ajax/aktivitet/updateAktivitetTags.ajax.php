<?php

use UKMNorge\Arrangement\Aktivitet\Aktivitet;
use UKMNorge\Arrangement\Aktivitet\AktivitetTag;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;


$handleCall = new HandleAPICall(['aktivitetId'], ['tags'], ['POST'], false);


$aktivitetId = $handleCall->getArgument('aktivitetId');
$tags = $handleCall->getOptionalArgument('tags');

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

$tags = $tags ? (array)$tags : [];
$tagsArr = [];

try {
    foreach($tags as $tagId) {
        $tagsArr[] = AktivitetTag::getById($tagId);
    }
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke lagre tagger', 401);
}


$res = Write::addTagsToAktivitet($aktivitet, $tagsArr);


$handleCall->sendToClient(['completed' => $res]);