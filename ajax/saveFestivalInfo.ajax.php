<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\Write as WriteArrangement;
use UKMNorge\Meta\Write as WriteMeta;


$handleCall = new HandleAPICall(["navn", "sted" ,"start" ,"end" ,"status"], [], ['GET', 'POST'], false);

$arrangement = null;
$navn = $handleCall->getArgument('navn');
$sted = $handleCall->getArgument('sted');
$start = date("Y-m-d H:i:s", $handleCall->getArgument('start'));
$end = date("Y-m-d H:i:s", $handleCall->getArgument('end'));
$status = $handleCall->getArgument('status');

try{
    $arrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

try {
    $arrangement->setNavn($navn);
    $arrangement->setSted($sted);
    $arrangement->setStart(DateTime::createFromFormat('Y-m-d H:i:s', $start));
    $arrangement->setStop(DateTime::createFromFormat('Y-m-d H:i:s', $end));
    
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
    $statusText = $status == 1 ? 'videresending_kanskje' : ($status == 2 ? 'videresending_sikkert' : 'gjennomforer');
    $arrangement->getMeta('avlys')->set($statusText);
} catch (Exception $e) {
    $handleCall->sendErrorToClient('En feil oppsto under lagring, og handlingen ble ikke fullført. Vennligst prøv igjen eller kontakt support dersom problemet vedvarer.', 500);
}

$handleCall->sendToClient([
    'success' => true,
    'message' => 'Arrangementet ble lagret'
]);