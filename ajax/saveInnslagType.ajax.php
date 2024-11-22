<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\Write;
use UKMNorge\Innslag\Typer\Typer;
use UKMNorge\Log\Logger;



$handleCall = new HandleAPICall(["selectedTyper"], [], ['GET', 'POST'], false);

$selectedTyper = $handleCall->getArgument('selectedTyper') ? $handleCall->getArgument('selectedTyper') : [];

$arrangement = null;

try{
    $arrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

try {

    $arrangement->getInnslagtyper()->getAll(); // laster de inn
    

    foreach (Typer::getAllTyper() as $type) {
        // if selectedVise does not contain the type, remove it
        if(!in_array($type->getKey(), $selectedTyper)) {
            try {
                $arrangement->getInnslagtyper()->fjern(Typer::getByKey($type->getKey()));
            } catch (Exception $e) {
                if ($e->getCode() != 110001) {
                    throw $e;
                }
            }
        } else {
            $arrangement->getInnslagtyper()->leggTil(Typer::getByKey($type->getKey()));
        }
    }

    // // If it is workshop
    // if ($arrangement->erArrangement()) {
    //     $arrangement->getInnslagTyper()->leggTil(Typer::getByKey('enkeltperson'));
    // } elseif ($arrangement->getInnslagTyper()->getAntall() == 0) {
    //     var_dump($arrangement->getInnslagTyper()->getAntall());
    //     $handleCall->sendErrorToClient('Det er ikke mulig å ha et arrangement med påmelding uten noen tillatte kategorier. Vi har derfor åpnet for standard-kategoriene, men du må gjerne redigere de.', 500);

    // }

    Logger::initWP($arrangement->getId());


    Write::save($arrangement);

} catch (Exception $e) {
    var_dump($e);
    $handleCall->sendErrorToClient('En feil oppsto under lagring av typer. Vennligst prøv igjen eller kontakt support dersom problemet vedvarer.', 500);
}

$handleCall->sendToClient([
    'success' => true,
    'message' => 'Arrangementet ble lagret'
]);










