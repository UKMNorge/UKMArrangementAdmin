<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\Write as WriteArrangement;
use UKMNorge\Meta\Write as WriteMeta;


$handleCall = new HandleAPICall(["name", "place", "startDate", "endDate", "status", "antallDeltakere", "openPamelding", "openVideresending",], [], ['GET', 'POST'], false);

$name = $handleCall->getArgument('name');
$place = $handleCall->getArgument('place');
$startDate = date("Y-m-d H:i:s", $handleCall->getArgument('startDate'));
$endDate = date("Y-m-d H:i:s", $handleCall->getArgument('endDate'));
$status = $handleCall->getArgument('status');
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
    $arrangement->setNavn($name);
    $arrangement->setSted($place);
    $arrangement->setStart(DateTime::createFromFormat('Y-m-d H:i:s', $startDate));
    $arrangement->setStop(DateTime::createFromFormat('Y-m-d H:i:s', $endDate));

    $arrangement->setDeltakereSynlig($antallDeltakere == 'true');
    $arrangement->setPamelding($openPamelding == 'true' ? 'apen' : 'ingen');
    $arrangement->setHarVideresending($openVideresending == 'true');

    // Lagrer basis info
    WriteArrangement::save($arrangement);

    // Lagrer status
    // 0 = gjennomføres 1 = kanskje, 2 = avlyses, 
    $statusText = $status == 1 ? 'videresending_kanskje' : ($status == 2 ? 'videresending_sikkert' : 'gjennomforer');
    $meta = $arrangement->getMeta('avlys')->set($statusText);

    if($status == 0) {
        WriteMeta::delete($meta);
    } else {
        WriteMeta::set($meta);
    }

} catch (Exception $e) {
    $handleCall->sendErrorToClient('En feil oppsto under lagring, og handlingen ble ikke fullført. Vennligst prøv igjen eller kontakt support dersom problemet vedvarer.', 500);
}

$handleCall->sendToClient([
    'success' => true,
    'message' => 'Arrangementet ble lagret'
]);










