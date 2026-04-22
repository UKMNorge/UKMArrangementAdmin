<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Videresending\Write;

$handleCall = new HandleAPICall(['nominasjonId'], [], ['GET', 'POST'], false);

$nominasjonId = (int) $handleCall->getArgument('nominasjonId');

try {
    $arrangement = new Arrangement(get_option('pl_id'));
} catch (Exception $e) {
    if ($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

if ($nominasjonId < 1) {
    $handleCall->sendToClient([
        'success' => false,
        'message' => 'Ugyldig nominasjonId',
    ]);
}

$target = null;
foreach (VideresendingNominasjon::getAlleTilArrangement($arrangement->getId())->getAll() as $vNominasjon) {
    if ((int) $vNominasjon->getId() === $nominasjonId) {
        $target = $vNominasjon;
        break;
    }
}

if ($target === null) {
    $handleCall->sendToClient([
        'success' => false,
        'message' => 'Fant ikke nominasjonen',
    ]);
}

try {
    // Send til deltaker etter at godkjenning er gjort fra motaker
    $target->sendTilDeltaker();
} catch (Exception $e) {
    $handleCall->sendToClient([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}

$handleCall->sendToClient([
    'success' => true,
    'message' => 'Nominasjon er merket som godkjent og sendes videre til deltaker',
]);

