<?php

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Nettverk\WriteOmradeKontaktperson;
use UKMNorge\Nettverk\OmradeKontaktpersoner;

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

$handleCall = new HandleAPICallWithAuthorization(['okpId', 'fornavn', 'etternavn'], ['epost', 'beskrivelse', 'deletedProfileImage'], ['POST'], false, false, $tilgang, $tilgangAttribute);

$id = $handleCall->getArgument('okpId');
$fornavn = $handleCall->getArgument('fornavn');
$etternavn = $handleCall->getArgument('etternavn');
$epost = $handleCall->getOptionalArgument('epost');
$beskrivelse = $handleCall->getOptionalArgument('beskrivelse') ?? '';
$deletedProfileImage = $handleCall->getOptionalArgument('deletedProfileImage') == 'true' ? true : false;

try {
    $okp = OmradeKontaktpersoner::getById($id);
    $okp->setFornavn($fornavn);
    $okp->setEtternavn($etternavn);
    $okp->setEpost($epost);
    $okp->setBeskrivelse($beskrivelse);

    $file = isset($_FILES['profile_picture']) ? $_FILES['profile_picture'] : ['size' => 0, 'name' => '', 'tmp_name' => ''];
    WriteOmradeKontaktperson::uploadProfileImage($file, $okp, $deletedProfileImage);
    WriteOmradeKontaktperson::editOmradekontaktperson($okp);
} catch (Exception $e) {
    $handleCall->sendErrorToClient($e->getMessage(), 400);
}

$handleCall->sendToClient([
    'success' => true,
    'message' => 'Kontaktperson ble lagret'
]);