<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall([],[], ['GET', 'POST'], false);

$arrangement = null;

try{
    $arrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$retArr = [
    "id" => $arrangement->getId(),
    "name" => $arrangement->getNavn(),
    "place" => $arrangement->getSted(),
    "startDate" => $arrangement->getStart()->getTimestamp(),
    "endDate" => $arrangement->getStop()->getTimestamp(),
    "status" => $arrangement->getMetaValue('avlys'),
    "statusKortText" => $arrangement->getMetaValue('avlys_status_kort'),
    "statusLangText" => $arrangement->getMetaValue('avlys_status_lang'),
    "antallDeltakere" => $arrangement->erDeltakereSynlig(),
    "openPamelding" => $arrangement->getPamelding() == 'apen',
    "openVideresending" => $arrangement->harVideresending(),
    "viseFrist" => $arrangement->getFrist1()->getTimestamp(),
    "jobbeFrist" => $arrangement->getFrist2()->getTimestamp(),
    "nettsideUrl" => $arrangement->getArrangementNettsideURL(),
    // Spesifikk til landsfestivalen
    "kvote_deltakere" => $arrangement->getMetaValue("kvote_deltakere"),
    "kvote_ledere" => $arrangement->getMetaValue("kvote_ledere"),
    "avgift_ordinar" => $arrangement->getMetaValue("avgift_ordinar"),
    "avgift_subsidiert" => $arrangement->getMetaValue("avgift_subsidiert"),
    "avgift_reise" => $arrangement->getMetaValue("avgift_reise"),

];

$handleCall->sendToClient($retArr);