<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\Program\Write;


$handleCall = new HandleAPICall(['id'], [], ['GET', 'POST'], false);

$id = $handleCall->getArgument('id') ? $handleCall->getArgument('id') : null;

$arrangement = null;

try{
    $arrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$res = false;
try {
    $res = Write::deleteHendelseGruppe($id, $arrangement->getId());

} catch (Exception $e) {
    $handleCall->sendErrorToClient('En feil oppsto under lagring av typer. Vennligst prøv igjen eller kontakt support dersom problemet vedvarer.', 500);
}

$handleCall->sendToClient([
    'success' => $res,
    'message' => 'Gruppe ble slettet'
]);










