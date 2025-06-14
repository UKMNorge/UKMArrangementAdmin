<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\Program\Write;
use UKMNorge\Innslag\Typer\Typer;
use UKMNorge\Log\Logger;



$handleCall = new HandleAPICall(['id', 'navn'], ['beskrivelse', 'hendelser', 'tag'], ['GET', 'POST'], false);

$id = $handleCall->getArgument('id') ? $handleCall->getArgument('id') : null;
$navn = $handleCall->getArgument('navn');
$beskrivelse = $handleCall->getOptionalArgument('beskrivelse') ? $handleCall->getOptionalArgument('beskrivelse') : '';
$selectedHendelser = $handleCall->getOptionalArgument('hendelser') ? $handleCall->getOptionalArgument('hendelser') : [];
$tag = $handleCall->getOptionalArgument('tag') ? $handleCall->getOptionalArgument('tag') : '';

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
    Logger::initWP($arrangement->getId());

    Write::createOrUpdateHendelseGruppe($id, $navn, $beskrivelse, $arrangement->getId(), $selectedHendelser, $tag);

} catch (Exception $e) {
    $handleCall->sendErrorToClient('En feil oppsto under lagring av typer. Vennligst prøv igjen eller kontakt support dersom problemet vedvarer.', 500);
}

$handleCall->sendToClient([
    'success' => true,
    'message' => 'Gruppen ble lagret'
]);










