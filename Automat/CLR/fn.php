<?php

function GenInfosCdr($licdr = array(), $config, $fileName) {
    global $rep_log_ignored;
    $licdr['montant'] = 0;
    $i = 1;
    while ($i <= 10 && $licdr['compteur' . $i]) {
        if ($licdr['compteur' . $i] == 2000)
            $licdr['montant']+= $licdr['montant' . $i];
        $i++;
    }
    if (!$licdr['montant'])
        return $licdr;
    $licdr['msisdn'] = verifierNumero($licdr['msisdn'], $config);
    if (strlen($licdr['msisdn']) == $config['ln_int'] &&
            substr($licdr['msisdn'], 0, strlen($config['code_op'])) == $config['code_op']
    ) {
        $licdr['considere'] = 1;
    } else {
        $fo = fopen($rep_log_ignored . "CLR_" . $fileName, 'a');
        $licdr['cause'] = 'MSISDN non valide';
        fputs($fo, json_encode($licdr) . "\r\n");
        fclose($fo);
        return $licdr;
    }
    return $licdr;
}

function GenRq(&$tb, $tbc) {
    // Generation Req Attribut
    global $allRq, $tbMSISDN, $cdrsFile, $tbAllTables, $rep_log, $config, $allRqAttr;
    $tmCdr = $tb['heure'];
    $MSISDN = $tb['msisdn'];
    $dj = substr($tmCdr, 0, 8);
    $dm = substr($tmCdr, 0, 6);
    $da = substr($tmCdr, 0, 4);
    $tabChNb = $tabChVal = $tabChCout = array();
    if (!in_array($MSISDN, $tbMSISDN))
        $tbMSISDN[] = $MSISDN;
    if (isset($tbc[$dj])) {
        $nmCh = $tbc[$dj];
        $tabChNb[$nmCh] = 1;
        $tabChVal[$nmCh] = $tb['montant'];
    }

    if (isset($tbc[$dm])) {
        $nmCh = $tbc[$dm];
        $tabChNb[$nmCh] = 1;
        $tabChVal[$nmCh] = $tb['montant'];
    }
    if (isset($tbc[$da])) {
        $nmCh = $tbc[$da];
        $tabChNb[$nmCh] = 1;
        $tabChVal[$nmCh] = $tb['montant'];
    }
    $tb['consommation'] = 0;
    fn_gen_rq("data_consommation_total", $MSISDN, $tabChVal);
    fn_gen_rq("data_change_balance_nombre_clr", $MSISDN, $tabChNb);
    fn_gen_rq("data_change_balance_valeur_clr", $MSISDN, $tabChVal);
    if (!isset($allRqAttr['autre'][$MSISDN]['heure']) || $allRqAttr['autre'][$MSISDN]['heure'] < $tmCdr) {
        $allRqAttr['autre'][$MSISDN]['heure'] = $tmCdr;
        $allRqAttr['autre'][$MSISDN]['req_val'] = substr($tb['status'], 0, 1). '||' . $tb['profil'];
    }
}

function execute_attribut($tbMSISDN, $allRqAttr, $connection) {
    try {
        if (count($tbMSISDN)) {
            $chaineMSISDN = "'" . implode("','", $tbMSISDN) . "'";
            $tbAttrMSISDNdate = array();

            $reqVerifNum = 'SELECT numero, dt_profil, dt_status	FROM data_attribut WHERE numero in (' . $chaineMSISDN . ')';

            $result = $connection->query($reqVerifNum);
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                $tbAttrMSISDNdate[$ligne->numero]['dt_profil'] = $ligne->dt_profil;
                $tbAttrMSISDNdate[$ligne->numero]['dt_status'] = $ligne->dt_status;
            }

            // ********* update de la table attr


            if (isset($allRqAttr['autre'])) {
                foreach ($allRqAttr['autre'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {
                        if ($tbAttrMSISDNdate[$ms]['dt_profil'] < $tb['heure'])
                            $cnd[] = "dt_profil = '" . $tb['heure'] . "', profil = " . $ChVals[1] ;
                        if ($tbAttrMSISDNdate[$ms]['dt_status'] < $tb['heure'])
                            $cnd[] = "dt_status = '" . $tb['heure'] . "', status = " . $ChVals[0];
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $req = "INSERT INTO data_attribut (numero, dt_profil, profil, dt_status, status) VALUES 
                        ('" . $ms . "', '" . $tb['heure'] . "', " . $ChVals[1] . ", '" . $tb['heure'] . "', " . $ChVals[0] . ")";
                    }
                    if ($req != '') {
//                        echo "\r\n  <br>" . $req . ';';
                        $result = $connection->query($req);
                    }
                }
            }
        }
    } catch (Exception $e) {
        throw($e);
    }
}

?>