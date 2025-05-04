<?php

use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;


$smsCode = null;
try{
    $smsCode = AktivitetTidspunkt::registrerDeltakerPaamelding(221, '46500000');
} catch(Exception $e) {
    echo $e->getMessage();
    die;
}

echo 'Deltaker registrert med sms-kode: ' . $smsCode;
