<?php

function GenInfosCdr($licdr = array(), $config, $fileName) {
    global $rep_log_ignored, $monServices, $operationsIdExclus;
    if ($licdr['msisdn'] == "" || in_array($licdr['service'], $monServices) || in_array($licdr['OperationID'], $operationsIdExclus) || ($licdr['OperationID'] == "" && $licdr['cout'] == ""))
        return $licdr;
    $licdr['msisdn'] = verifierNumero($licdr['msisdn'], $config);
    $licdr['msisdn_autre'] = verifierNumero($licdr['msisdn_autre'], $config);
    if (strlen($licdr['msisdn']) == $config['ln_int'] &&
            substr($licdr['msisdn'], 0, strlen($config['code_op'])) == $config['code_op']
    ) {
        $licdr['considere'] = 1;
        $licdr['prefix']['total'] = 1;
    } else {
        $fo = fopen($rep_log_ignored . "MGR_" . $fileName, 'a');
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
    $MSISDN_AU = $tb['msisdn_autre'];
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
        $tabChCout[$nmCh] = $tb['cout'];
    }

    if (isset($tbc[$dm])) {
        $nmCh = $tbc[$dm];
        $tabChNb[$nmCh] = 1;
        $tabChVal[$nmCh] = $tb['montant'];
        $tabChCout[$nmCh] = $tb['cout'];
    }
    if (isset($tbc[$da])) {
        $nmCh = $tbc[$da];
        $tabChNb[$nmCh] = 1;
        $tabChVal[$nmCh] = $tb['montant'];
        $tabChCout[$nmCh] = $tb['cout'];
    }

    if ($tb['OperationID'] == 4052101 && $tb['request'] == 1) { // MGR OUT
        
        $tb['type_tr'] = 23;
        if (!in_array($tb['msisdn_autre'], $tbMSISDN))
            $tbMSISDN[] = $tb['msisdn_autre'];
        fn_gen_rq("data_mgr_nombre_out", $MSISDN, $tabChNb);
        fn_gen_rq("data_mgr_valeur_out", $MSISDN, $tabChVal);
        fn_gen_rq("data_mgr_nombre_in", $MSISDN_AU, $tabChNb);
        fn_gen_rq("data_mgr_valeur_in", $MSISDN_AU, $tabChVal);

        if ($tb['cout']) {
            $tb['consommation'] = $tb['cout'];
            fn_gen_rq("data_mgr_valeur_cout", $MSISDN, $tabChCout);
            fn_gen_rq("data_consommation_total", $MSISDN, $tabChCout);
        }else
            $tb['consommation'] = 0;

        if (!isset($allRqAttr['mgr'][$MSISDN]['heure']) || $allRqAttr['mgr'][$MSISDN]['heure'] < $tmCdr) {
            $allRqAttr['mgr'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['mgr'][$MSISDN]['req_val'] = $tb['balance']
                    . '||' . $tb['profil']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['montant']
                    . '||' . $tb['dt_active_stop']
                    . '||' . $tb['dt_suspend_stop']
                    . '||' . $tb['dt_disable_stop'];
        }

        //############ FIN MGR
    } elseif ($tb['OperationID'] == 4052101 && $tb['request'] == 0) { // Transfert IN
        $tb['type_tr'] = 22;
        if (!isset($allRqAttr['mgr_in'][$MSISDN]['heure']) || $allRqAttr['mgr_in'][$MSISDN]['heure'] < $tmCdr) {
            $allRqAttr['mgr_in'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['mgr_in'][$MSISDN]['req_val'] = $tb['balance']
                    . '||' . $tb['profil']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['montant']
                    . '||' . $tb['dt_active_stop']
                    . '||' . $tb['dt_suspend_stop']
                    . '||' . $tb['dt_disable_stop'];
        }
        $tb['consommation'] = 0;
        //############ FIN TRANS IN
    } elseif ($tb['OperationID'] == 4052485) { //MON Service payant avec id
        $idService = $tb['service'];
        $tb['consommation'] = $tb['cout'];
        fn_gen_rq("data_consommation_total", $MSISDN, $tabChCout);
        fn_gen_rq("data_service_nombre_all", $MSISDN, $tabChNb);
        fn_gen_rq("data_service_valeur_all", $MSISDN, $tabChCout);
        if (!in_array("data_service_nombre_$idService", $tbAllTables) || !in_array("data_service_valeur_$idService", $tbAllTables)) {
            $fo = fopen($rep_log . "log_mgr_srv_" . $idService, 'a');
            fputs($fo, json_encode($tb) . "\r\n");
            fclose($fo);
        } else {
            fn_gen_rq("data_service_nombre_$idService", $MSISDN, $tabChNb);
            fn_gen_rq("data_service_valeur_$idService", $MSISDN, $tabChCout);
            //mise a jours des agrÃ©gations de valeur et nombre pour ce service (data, sms, voix et time)
            $tb['allservice'] = $idService;
            if (isset($config['agreg'][$idService])) {
                $tb[$config['agreg'][$idService]] = $idService;
                fn_gen_rq("data_service_nombre_" . $config['agreg'][$idService], $MSISDN, $tabChNb);
                fn_gen_rq("data_service_valeur_" . $config['agreg'][$idService], $MSISDN, $tabChCout);
            }
        }

        if (!isset($allRqAttr['service'][$MSISDN]['heure']) || $allRqAttr['service'][$MSISDN]['heure'] < $tmCdr) {
            $allRqAttr['service'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['service'][$MSISDN]['req_val'] = $tb['balance']
                    . '||' . $tb['profil']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['service']
                    . '||' . $tb['dt_active_stop']
                    . '||' . $tb['dt_suspend_stop']
                    . '||' . $tb['dt_disable_stop'];
        }
//        echo "\n\r $MSISDN Souscription service $idService ";
        //############ FIN SERVICE
    } elseif ($tb['cout'] != 0 && $tb['OperationID'] == "") { // Sous service
        $tb['consommation'] = $tb['cout'];
        fn_gen_rq("data_consommation_total", $MSISDN, $tabChCout);
        fn_gen_rq("data_service_nombre_all", $MSISDN, $tabChNb);
        fn_gen_rq("data_service_valeur_all", $MSISDN, $tabChCout);
        fn_gen_rq("data_service_nombre_sous_service", $MSISDN, $tabChNb);
        fn_gen_rq("data_service_valeur_sous_service", $MSISDN, $tabChCout);

        if (!isset($allRqAttr['autre'][$MSISDN]['heure']) || $allRqAttr['autre'][$MSISDN]['heure'] < $tmCdr) {
            $allRqAttr['autre'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['autre'][$MSISDN]['req_val'] = $tb['balance']
                    . '||' . $tb['service']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['dt_active_stop']
                    . '||' . $tb['dt_suspend_stop']
                    . '||' . $tb['dt_disable_stop'];
        }
        //############ FIN SOUS SERVICE
    } elseif ($tb['chargeFromAcount'] < 0 && $tb['OperationID'] == 4052100) { // Dépot de crédit
        $tb['consommation'] = 0;
        foreach ($tabChVal as $dt => $val)
            $tabChVal[$dt] = -1 * $tb['chargeFromAcount'];

        fn_gen_rq("data_change_balance_nombre_depot", $MSISDN, $tabChNb);
        fn_gen_rq("data_change_balance_valeur_depot", $MSISDN, $tabChVal);
        if (!isset($allRqAttr['autre'][$MSISDN]['heure']) || $allRqAttr['autre'][$MSISDN]['heure'] < $tmCdr) {
            $allRqAttr['autre'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['autre'][$MSISDN]['req_val'] = $tb['balance']
                    . '||' . $tb['profil']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['dt_active_stop']
                    . '||' . $tb['dt_suspend_stop']
                    . '||' . $tb['dt_disable_stop'];
        }

        //############ FIN DÃ©pot
    } elseif ($tb['chargeFromAcount'] > 0 && $tb['OperationID'] == 4052100) { // Retranche de crédit
        $tb['consommation'] = 0;
        foreach ($tabChVal as $dt => $val)
            $tabChVal[$dt] = $tb['chargeFromAcount'];
        fn_gen_rq("data_change_balance_nombre_retranche", $MSISDN, $tabChNb);
        fn_gen_rq("data_change_balance_valeur_retranche", $MSISDN, $tabChVal);
        if (!isset($allRqAttr['autre'][$MSISDN]['heure']) || $allRqAttr['autre'][$MSISDN]['heure'] < $tmCdr) {
            $allRqAttr['autre'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['autre'][$MSISDN]['req_val'] = $tb['balance']
                    . '||' . $tb['profil']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['dt_active_stop']
                    . '||' . $tb['dt_suspend_stop']
                    . '||' . $tb['dt_disable_stop'];
        }

        //############ FIN Retranche
    } elseif ($tb['OperationID'] == 4052100 && $tb['old_status'] != $tb['status']) { // Changement de status
        $tb['consommation'] = 0;
        if (!isset($allRqAttr['autre'][$MSISDN]['heure']) || $allRqAttr['autre'][$MSISDN]['heure'] < $tmCdr) {
            $allRqAttr['autre'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['autre'][$MSISDN]['req_val'] = $tb['balance']
                    . '||' . $tb['profil']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['dt_active_stop']
                    . '||' . $tb['dt_suspend_stop']
                    . '||' . $tb['dt_disable_stop'];
        }
    } elseif ($tb['OperationID'] == 4057009 && $tb['old_status']) { // Changment de profil
        $tb['consommation'] = 0;
        if (!isset($allRqAttr['autre'][$MSISDN]['heure']) || $allRqAttr['autre'][$MSISDN]['heure'] < $tmCdr) {
            $allRqAttr['autre'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['autre'][$MSISDN]['req_val'] = $tb['balance']
                    . '||' . $tb['profil']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['dt_active_stop']
                    . '||' . $tb['dt_suspend_stop']
                    . '||' . $tb['dt_disable_stop'];
        }
    } else { // Cas non prévu, écrire dans le fichier log
        $tb['consommation'] = 0;
        $fo = fopen($rep_log . "log_mgr_CasNonPrevu_operId_" . $tb['OperationID'], 'a');
        fputs($fo, json_encode($tb) . "\r\n");
        fclose($fo);
    }
}

function execute_attribut($tbMSISDN, $allRqAttr, $connection) {
    try {
        if (count($tbMSISDN)) {
            $chaineMSISDN = "'" . implode("','", $tbMSISDN) . "'";
            $tbNtMSISDN = $tbAttrMSISDN = $tbAttrMSISDNdate = array();

            $reqVerifNum = 'SELECT numero, dt_service, dt_transfert_out, dt_transfert_in, 
			dt_balance, dt_profil, dt_status, dt_active_stop, dt_suspend_stop, dt_disable_stop 
			FROM data_attribut WHERE numero in (' . $chaineMSISDN . ')';

            $result = $connection->query($reqVerifNum);
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//            $tbAttrMSISDN[] = $ligne->numero;
                $tbAttrMSISDNdate[$ligne->numero]['dt_service'] = $ligne->dt_service;
                $tbAttrMSISDNdate[$ligne->numero]['dt_transfert_out'] = $ligne->dt_transfert_out;
                $tbAttrMSISDNdate[$ligne->numero]['dt_transfert_in'] = $ligne->dt_transfert_in;
                $tbAttrMSISDNdate[$ligne->numero]['dt_profil'] = $ligne->dt_profil;
                $tbAttrMSISDNdate[$ligne->numero]['dt_balance'] = $ligne->dt_balance;
                $tbAttrMSISDNdate[$ligne->numero]['dt_status'] = $ligne->dt_status;
                $tbAttrMSISDNdate[$ligne->numero]['dt_active_stop'] = $ligne->dt_active_stop;
                $tbAttrMSISDNdate[$ligne->numero]['dt_suspend_stop'] = $ligne->dt_suspend_stop;
                $tbAttrMSISDNdate[$ligne->numero]['dt_disable_stop'] = $ligne->dt_disable_stop;
            }

            // ********* update de la table attr

            if (isset($allRqAttr['service'])) {
                foreach ($allRqAttr['service'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {
                        if ($tbAttrMSISDNdate[$ms]['dt_balance'] < $tb['heure'])
                            $cnd[] = "dt_balance = '" . $tb['heure'] . "', balance = '" . $ChVals[0] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_profil'] < $tb['heure'])
                            $cnd[] = "dt_profil = '" . $tb['heure'] . "', profil = '" . $ChVals[1] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_status'] < $tb['heure'])
                            $cnd[] = "dt_status = '" . $tb['heure'] . "', status = '" . $ChVals[2] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_service'] < $tb['heure'])
                            $cnd[] = "dt_service = '" . $tb['heure'] . "', service = '" . $ChVals[3] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_active_stop'] < $ChVals[4])
                            $cnd[] = "dt_active_stop = '" . $ChVals[4] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_suspend_stop'] < $ChVals[5])
                            $cnd[] = "dt_suspend_stop = '" . $ChVals[5] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_disable_stop'] < $ChVals[6])
                            $cnd[] = "dt_disable_stop = '" . $ChVals[6] . "' ";
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $req = "INSERT INTO data_attribut (numero, dt_balance, balance, dt_profil, profil, dt_status, status,
                        dt_service, service, dt_active_stop, dt_suspend_stop, dt_disable_stop)  VALUES
                        ('" . $ms . "', '" . $tb['heure'] . "', '" . $ChVals[0] . "', '" . $tb['heure'] . "', '" . $ChVals[1] . "',
                            '" . $tb['heure'] . "', '" . $ChVals[2] . "', '" . $tb['heure'] . "', '" . $ChVals[3] . "', '" . $ChVals[4] . "', 
                                '" . $ChVals[5] . "', '" . $ChVals[6] . "')";
                        $tbAttrMSISDNdate[$ms]['dt_service'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_transfert_out'] = '';
                        $tbAttrMSISDNdate[$ms]['dt_transfert_in'] = '';
                        $tbAttrMSISDNdate[$ms]['dt_profil'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_balance'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_status'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_active_stop'] = $ChVals[4];
                        $tbAttrMSISDNdate[$ms]['dt_suspend_stop'] = $ChVals[5];
                        $tbAttrMSISDNdate[$ms]['dt_disable_stop'] = $ChVals[6];
                    }
                    if ($req != '') {
//                        echo "\r\n <br>" . $req . ';';
                        $result = $connection->query($req);
                    }
                }
            }

            if (isset($allRqAttr['mgr'])) {
                foreach ($allRqAttr['mgr'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {
                        if ($tbAttrMSISDNdate[$ms]['dt_balance'] < $tb['heure'])
                            $cnd[] = "dt_balance = '" . $tb['heure'] . "', balance = '" . $ChVals[0] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_profil'] < $tb['heure'])
                            $cnd[] = "dt_profil = '" . $tb['heure'] . "', profil = '" . $ChVals[1] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_status'] < $tb['heure'])
                            $cnd[] = "dt_status = '" . $tb['heure'] . "', status = '" . $ChVals[2] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_transfert_out'] < $tb['heure'])
                            $cnd[] = "dt_transfert_out = '" . $tb['heure'] . "', montant_transfere_out = '" . $ChVals[3] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_active_stop'] < $ChVals[4])
                            $cnd[] = "dt_active_stop = '" . $ChVals[4] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_suspend_stop'] < $ChVals[5])
                            $cnd[] = "dt_suspend_stop = '" . $ChVals[5] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_disable_stop'] < $ChVals[6])
                            $cnd[] = "dt_disable_stop = '" . $ChVals[6] . "' ";
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $req = "INSERT INTO data_attribut (numero, dt_balance, balance, dt_profil, profil, dt_status, status,
                        dt_transfert_out, montant_transfere_out, dt_active_stop, dt_suspend_stop, dt_disable_stop) VALUES
                        ('" . $ms . "', '" . $tb['heure'] . "', '" . $ChVals[0] . "', '" . $tb['heure'] . "', '" . $ChVals[1] . "',
                         '" . $tb['heure'] . "', '" . $ChVals[2] . "', '" . $tb['heure'] . "', '" . $ChVals[3] . "',
                         '" . $ChVals[4] . "', '" . $ChVals[5] . "','" . $ChVals[6] . "')";
                        $tbAttrMSISDNdate[$ms]['dt_service'] = '';
                        $tbAttrMSISDNdate[$ms]['dt_transfert_out'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_transfert_in'] = '';
                        $tbAttrMSISDNdate[$ms]['dt_profil'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_balance'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_status'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_active_stop'] = $ChVals[4];
                        $tbAttrMSISDNdate[$ms]['dt_suspend_stop'] = $ChVals[5];
                        $tbAttrMSISDNdate[$ms]['dt_disable_stop'] = $ChVals[6];
                    }
                    if ($req != '') {
//                        echo "\r\n  <br>" . $req . ';';
                        $result = $connection->query($req);
                    }
                }
            }

            if (isset($allRqAttr['mgr_in'])) {
                foreach ($allRqAttr['mgr_in'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {
                        if ($tbAttrMSISDNdate[$ms]['dt_balance'] < $tb['heure'])
                            $cnd[] = "dt_balance = '" . $tb['heure'] . "', balance = '" . $ChVals[0] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_profil'] < $tb['heure'])
                            $cnd[] = "dt_profil = '" . $tb['heure'] . "', profil = '" . $ChVals[1] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_status'] < $tb['heure'])
                            $cnd[] = "dt_status = '" . $tb['heure'] . "', status = '" . $ChVals[2] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_transfert_in'] < $tb['heure'])
                            $cnd[] = "dt_transfert_in = '" . $tb['heure'] . "', montant_transfere_in = '" . $ChVals[3] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_active_stop'] < $ChVals[4])
                            $cnd[] = "dt_active_stop = '" . $ChVals[4] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_suspend_stop'] < $ChVals[5])
                            $cnd[] = "dt_suspend_stop = '" . $ChVals[5] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_disable_stop'] < $ChVals[6])
                            $cnd[] = "dt_disable_stop = '" . $ChVals[6] . "' ";
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $req = "INSERT INTO data_attribut (numero, dt_balance, balance, dt_profil, profil, dt_status, status, 
                        dt_transfert_in, montant_transfere_in, dt_active_stop, dt_suspend_stop, dt_disable_stop) VALUES 
                        ('" . $ms . "', '" . $tb['heure'] . "', '" . $ChVals[0] . "', '" . $tb['heure'] . "', '" . $ChVals[1] . "',
                              '" . $tb['heure'] . "', '" . $ChVals[2] . "', '" . $tb['heure'] . "',  '" . $ChVals[3] . "',
                              '" . $ChVals[4] . "', '" . $ChVals[5] . "', '" . $ChVals[6] . "')";
                        $tbAttrMSISDNdate[$ms]['dt_service'] = '';
                        $tbAttrMSISDNdate[$ms]['dt_transfert_out'] = '';
                        $tbAttrMSISDNdate[$ms]['dt_transfert_in'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_profil'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_balance'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_status'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_active_stop'] = $ChVals[4];
                        $tbAttrMSISDNdate[$ms]['dt_suspend_stop'] = $ChVals[5];
                        $tbAttrMSISDNdate[$ms]['dt_disable_stop'] = $ChVals[6];
                    }
                    if ($req != '') {
//                        echo "\r\n  <br>" . $req . ';';
                        $result = $connection->query($req);
                    }
                }
            }

            if (isset($allRqAttr['autre'])) {
                foreach ($allRqAttr['autre'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {
                        if ($tbAttrMSISDNdate[$ms]['dt_balance'] < $tb['heure'])
                            $cnd[] = "dt_balance = '" . $tb['heure'] . "', balance = '" . $ChVals[0] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_profil'] < $tb['heure'])
                            $cnd[] = "dt_profil = '" . $tb['heure'] . "', profil = '" . $ChVals[1] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_status'] < $tb['heure'])
                            $cnd[] = "dt_status = '" . $tb['heure'] . "', status = '" . $ChVals[2] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_active_stop'] < $ChVals[3])
                            $cnd[] = "dt_active_stop = '" . $ChVals[3] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_suspend_stop'] < $ChVals[4])
                            $cnd[] = "dt_suspend_stop = '" . $ChVals[4] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_disable_stop'] < $ChVals[5])
                            $cnd[] = "dt_disable_stop = '" . $ChVals[5] . "' ";
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $req = "INSERT INTO data_attribut (numero, dt_balance, balance, dt_profil, profil, dt_status, status, 
                        dt_active_stop, dt_suspend_stop, dt_disable_stop) VALUES 
                        ('" . $ms . "', '" . $tb['heure'] . "', '" . $ChVals[0] . "', '" . $tb['heure'] . "', '" . $ChVals[1] . "',
                              '" . $tb['heure'] . "', '" . $ChVals[2] . "',
                              '" . $ChVals[3] . "', '" . $ChVals[4] . "', '" . $ChVals[5] . "')";
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