<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\Write as WriteArrangement;
use UKMNorge\Meta\Write as WriteMeta;


$handleCall = new HandleAPICall(["antallDeltakere", "openPamelding" ,"openVideresending"], [], ['GET', 'POST'], false);


$antallDeltakere = $handleCall->getArgument('antallDeltakere');
$openPamelding = $handleCall->getArgument('openPamelding');
$openVideresending = $handleCall->getArgument('openVideresending');

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
    // Synlighet av deltakere
    $arrangement->setDeltakereSynlig($antallDeltakere == 'true');
    
    // PÅMELDING
    $arrangement->setPamelding($pamelding == 'true' ? 'apen' : 'ingen');

    // VIDERESENDING
    $arrangement->setHarVideresending($openVideresending == 'true');

    // Lagrer basis info
    WriteArrangement::save($arrangement);

} catch (Exception $e) {
    $handleCall->sendErrorToClient('En feil oppsto under lagring, og handlingen ble ikke fullført. Vennligst prøv igjen eller kontakt support dersom problemet vedvarer.', 500);
}

$handleCall->sendToClient([
    'success' => true,
    'message' => 'Arrangementet ble lagret'
]);















if (isset($_POST['synlig'])) {
    $arrangement->setSynlig($_POST['synlig'] == 'true');
}

// Synlighet av deltakere
if (isset($_POST['deltakeresynlig'])) {
    $arrangement->setDeltakereSynlig($_POST['deltakeresynlig'] == 'true');
}

// PÅMELDING
if (isset($_POST['pamelding'])) {
    $pamelding = str_replace(['true','false'],['apen','ingen'], $_POST['pamelding']);
    $arrangement->setPamelding($pamelding);
}

// VIDERESENDING
if (isset($_POST['videresending'])) {
    $arrangement->setHarVideresending($_POST['videresending'] == 'true');
}