<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\Videresending\Ledere\Ledere;
use UKMNorge\Arrangement\Videresending\Ledere\Write;


$handleCall = new HandleAPICall(['lederId', 'godkjenning'],[], ['GET', 'POST'], false);

$godkjenning = $handleCall->getArgument('name') == 'true';
$lederId = $handleCall->getArgument('lederId');


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


$isSaved = false;

foreach($tilArrangement->getVideresending()->getAvsendere() as $avsender) {
    $fra = $avsender->getArrangement();
    $fylke = $fra->getFylke();

    $ledere = new Ledere($fra->getId(), $tilArrangement->getId());
    foreach($ledere->getAll() as $leder) {
        if($lederId == $leder->getId()) {
            $leder->setGodkjent($godkjenning);
            $isSaved = Write::save( $leder );
            continue;
        }
    }
    
}

$handleCall->sendToClient([
    'success' => $isSaved,
    'message' => $isSaved ? 'Lederen ble lagret' : 'Lederen ble ikke lagret'
]);