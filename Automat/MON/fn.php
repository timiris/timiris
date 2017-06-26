<?php

function GenInfosCdr($licdr = array(), $config, $fileName) {
    global $rep_log_ignored;
    if ($licdr['service'] != "148211097" || $licdr['cout'] == '')
        return $licdr;
    $licdr['msisdn'] = verifierNumero($licdr['msisdn'], $config);
    if (strlen($licdr['msisdn']) == $config['ln_int'] &&
            substr($licdr['msisdn'], 0, strlen($config['code_op'])) == $config['code_op']
    ) {
        $licdr['considere'] = 1;
        $licdr['prefix']['total'] = 1;
    } else {
        $fo = fopen($rep_log_ignored . "MON_" . $fileName, 'a');
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
        $tabChCout[$nmCh] = $tb['cout'];
    }

    if (isset($tbc[$dm])) {
        $nmCh = $tbc[$dm];
        $tabChNb[$nmCh] = 1;
        $tabChCout[$nmCh] = $tb['cout'];
    }
    if (isset($tbc[$da])) {
        $nmCh = $tbc[$da];
        $tabChNb[$nmCh] = 1;
        $tabChCout[$nmCh] = $tb['cout'];
    }
    $tb['consommation'] = $tb['cout'];
    $idService = $tb['service'];
    fn_gen_rq("data_consommation_total", $MSISDN, $tabChCout);
    fn_gen_rq("data_service_nombre_all", $MSISDN, $tabChNb);
    fn_gen_rq("data_service_valeur_all", $MSISDN, $tabChCout);
    if (!in_array("data_service_nombre_$idService", $tbAllTables) || !in_array("data_service_valeur_$idService", $tbAllTables)) {
        $fo = fopen($rep_log . "log_mon_srv_" . $idService, 'a');
        fputs($fo, json_encode($tb) . "\r\n");
        fclose($fo);
    } else {
        fn_gen_rq("data_service_nombre_$idService", $MSISDN, $tabChNb);
        fn_gen_rq("data_service_valeur_$idService", $MSISDN, $tabChCout);
        if (isset($config['agreg'][$idService])) {
            $tb[$config['agreg'][$idService]] = $idService;
            fn_gen_rq("data_service_nombre_" . $config['agreg'][$idService], $MSISDN, $tabChNb);
            fn_gen_rq("data_service_valeur_" . $config['agreg'][$idService], $MSISDN, $tabChCout);
        }
    }

    if (!isset($allRqAttr['service'][$MSISDN]['heure']) || $allRqAttr['service'][$MSISDN]['heure'] < $tmCdr) {
        $allRqAttr['service'][$MSISDN]['heure'] = $tmCdr;
        $allRqAttr['service'][$MSISDN]['req_val'] = $tb['profil']. '||' . substr($tb['status'], 0, 1). '||' . $tb['service'];
    }
}

function execute_attribut($tbMSISDN, $allRqAttr, $connection) {
    try {
        if (count($tbMSISDN)) {
            $chaineMSISDN = "'" . implode("','", $tbMSISDN) . "'";
            $tbNtMSISDN = $tbAttrMSISDN = $tbAttrMSISDNdate = array();

            $reqVerifNum = 'SELECT numero, dt_service, dt_profil, dt_status 
			FROM data_attribut WHERE numero in (' . $chaineMSISDN . ')';

            $result = $connection->query($reqVerifNum);
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//            $tbAttrMSISDN[] = $ligne->numero;
                $tbAttrMSISDNdate[$ligne->numero]['dt_service'] = $ligne->dt_service;
                $tbAttrMSISDNdate[$ligne->numero]['dt_profil'] = $ligne->dt_profil;
                $tbAttrMSISDNdate[$ligne->numero]['dt_status'] = $ligne->dt_status;
            }

            // ********* update de la table attr

            if (isset($allRqAttr['service'])) {
                foreach ($allRqAttr['service'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {
                        if ($tbAttrMSISDNdate[$ms]['dt_profil'] < $tb['heure'])
                            $cnd[] = "dt_profil = '" . $tb['heure'] . "', profil = " . $ChVals[0] ;
                        if ($tbAttrMSISDNdate[$ms]['dt_status'] < $tb['heure'])
                            $cnd[] = "dt_status = '" . $tb['heure'] . "', status = " . $ChVals[1] ;
                        if ($tbAttrMSISDNdate[$ms]['dt_service'] < $tb['heure'])
                            $cnd[] = "dt_service = '" . $tb['heure'] . "', service = '" . $ChVals[2] . "'";
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $req = "INSERT INTO data_attribut (numero, dt_profil, profil, dt_status, status, dt_service, service)
                            VALUES
                        ('" . $ms . "', '" . $tb['heure'] . "', " . $ChVals[0] . ", '" . $tb['heure'] . "', " . $ChVals[1] . ",
                            '" . $tb['heure'] . "', '" . $ChVals[2] . "')";
                        $tbAttrMSISDNdate[$ms]['dt_service'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_profil'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_status'] = $tb['heure'];
                    }
                    if ($req != '') {
//                        echo "\r\n <br>" . $req . ';';
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