<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\Videresending\Ledere\Ledere;



$handleCall = new HandleAPICall([],[], ['GET', 'POST'], false);

$tilArrangement = null;
$alleTyper = null;

try{
    $tilArrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangement eller typer', 401);
}


$retLedere = [];

foreach($tilArrangement->getVideresending()->getAvsendere() as $avsender) {
    $fra = $avsender->getArrangement();
    $fylke = $fra->getFylke();

    $ledere = new Ledere($fra->getId(), $tilArrangement->getId());
    
    foreach($ledere->getAll() as $leder) {
        $lederObj = [
            'id' => $leder->getId(),
            'navn' => $leder->getNavn(),
            'epost' => $leder->getEpost(),
            'mobil' => $leder->getMobil(),
            'type' => $leder->getTypeNavn(),
            'fraArrangementNavn' => $leder->getArrangementFra()->getNavn(),
            'fraFylkeNavn' => $fylke->getNavn(),
            'beskrivelse' => $leder->getBeskrivelse(),
            'godkjent' => $leder->getGodkjent(),
        ];

        $retLedere[] = $lederObj;
    }

}

$handleCall->sendToClient($retLedere);