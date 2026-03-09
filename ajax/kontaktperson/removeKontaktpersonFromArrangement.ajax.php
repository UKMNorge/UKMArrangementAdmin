<?php

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Nettverk\Omrade;
use UKMNorge\Nettverk\OmradeKontaktpersoner;
use UKMNorge\Nettverk\WriteOmradeKontaktperson;

require_once('UKM/Autoloader.php');

$omradeId = HandleAPICallWithAuthorization::getArgumentBeforeInit('omradeId', 'POST');
$omradeType = HandleAPICallWithAuthorization::getArgumentBeforeInit('omradeType', 'POST');

if(($omradeType != 'fylke' && $omradeType != 'kommune' && $omradeType != 'monstring') || $omradeType == null) {
    HandleAPICallWithAuthorization::sendError("Støtter område type 'fylke', 'monstring' eller 'kommune'", 400);
}

if($omradeType == 'monstring') {
    $tilgang = 'arrangement_i_kommune_fylke';
}
else {
    $tilgang = $omradeType == 'kommune' ? 'kommune_eller_fylke' : 'fylke';
}
$tilgangAttribute = $omradeId;

$handleCall = new HandleAPICallWithAuthorization(['id'], [], ['POST'], false, false, $tilgang, $tilgangAttribute);

$id = $handleCall->getArgument('id');

try {
    $okp = OmradeKontaktpersoner::getById($id);
    $arrangementOmrade = new Omrade($omradeType, $omradeId);
    WriteOmradeKontaktperson::removeFromOmrade($okp, $arrangementOmrade);
} catch (Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke slette kontaktperson. Systemet sa: ' . $e->getMessage(), 400);
}

$handleCall->sendToClient([
    'success' => true,
    'message' => 'Kontaktperson ble fjernet'
]);
