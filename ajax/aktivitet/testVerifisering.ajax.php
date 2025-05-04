<?php

use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;


$verifisering = false;
try{
    $verifisering = AktivitetTidspunkt::verifyDeltakerPaamelding(221, '46500000', 'EGMN7O');
} catch(Exception $e) {
    echo $e->getMessage();
    die;
}

echo 'Verifisering resultat: ' . $verifisering;
