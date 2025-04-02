<?php

use UKMNorge\Arrangement\Aktivitet\AktivitetTag;
use UKMNorge\Arrangement\Aktivitet\Write;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;


$handleCall = new HandleAPICall(['tagId'], [], ['POST'], false);

$tagId = $handleCall->getArgument('tagId');

$tag = null;
try{
    $tag = AktivitetTag::getById($tagId);

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

// Slett alle relationships først
// foreach($aktivitet->getTidspunkter()->getAll() as $tidspunkt) {
//     Write::deleteAktivitetTidspunkt($tidspunkt);
// }

$res = Write::deleteTag($tag);

$handleCall->sendToClient(['completed' => $res == true]);