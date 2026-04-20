<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Innslag\Innslag;


$handleCall = new HandleAPICall([],[], ['GET', 'POST'], false);

try{
    $arrangement = new Arrangement(get_option('pl_id'));

} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$retArr = [];

foreach (VideresendingNominasjon::getAlleTilArrangement($arrangement->getId())->getAll() as $vNominasjon) {
    $objNominasjon = $vNominasjon->getArrObj();

    $innslag = Innslag::getById($objNominasjon['b_id']);
    $innslagObj = [
        'id' => $innslag->getId(),
        'navn' => $innslag->getNavn(),
        'type' => $innslag->getType()->getNavn(),
        'sjanger' => $innslag->getSjanger(),
        'beskrivelse' => $innslag->getBeskrivelse(),
        'omrade_navn' => $innslag->getOmradeNavn(),
        'har_titler' => $innslag->getType()->harTitler(),
        'type_key' => $innslag->getType()->getKey(),
        'fylke_fra' => $vNominasjon->getArrangementFra()->getFylke()->getNavn(),
        'fylke_fra_id' => $vNominasjon->getArrangementFra()->getFylke()->getId(),
    ];

    $innslagId = $innslag->getId();

    if (!isset($retArr[$innslagId])) {
        $retArr[$innslagId] = [
            'innslag' => $innslagObj,
            'titler' => [],
        ];
    }

    if ($innslag->getType()->harTitler()) {
        foreach ($innslag->getTitler()->getAll() as $tittel) {
            if ($tittel->getId() == $objNominasjon['t_id']) {
                $tittelId = $tittel->getId();
                if (!isset($retArr[$innslagId]['titler'][$tittelId])) {
                    $retArr[$innslagId]['titler'][$tittelId] = [
                        'id' => $tittelId,
                        'navn' => $tittel->getNavn(),
                        'nominasjoner' => [],
                    ];
                }
                $retArr[$innslagId]['titler'][$tittelId]['nominasjoner'][] = $objNominasjon;
                break;
            }
        }
    } else {
        if (!isset($retArr[$innslagId]['nominasjoner'])) {
            $retArr[$innslagId]['nominasjoner'] = [];
        }
        $retArr[$innslagId]['nominasjoner'][] = $objNominasjon;
    }
}

foreach ($retArr as &$gruppe) {
    if (!empty($gruppe['titler'])) {
        $gruppe['titler'] = array_values($gruppe['titler']);
    }
}
unset($gruppe);

$handleCall->sendToClient(array_values($retArr));