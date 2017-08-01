<?php

function GenInfosCdr($licdr = array(), $config, $fileName) {
    global $rep_log_ignored;
    $licdr['val_facial'] = $licdr['valeur'] / 100;
    $licdr['msisdn'] = verifierNumero($licdr['msisdn'], $config);
    if (strlen($licdr['msisdn']) != $config['ln_int'] || substr($licdr['msisdn'], 0, strlen($config['code_op'])) != $config['code_op']) {
        $licdr['cause'] = 'MSISDN non valide';
        $fo = fopen($rep_log_ignored . "VOU_" . $fileName, 'a');
        fputs($fo, json_encode($licdr) . "\r\n");
        fclose($fo);
        return $licdr;
    }
    if ($licdr['canal'] != 0 && $licdr['valeur'] == 0)
        return $licdr;
    $licdr['considere'] = 1;
    if ($licdr['canal'] != 0 && $licdr['valeur'] != 0) {
        $licdr['prefix']['total'] = 1;
        if (isset($config['canal'][$licdr['canal']]))
            $licdr['prefix'][$config['canal'][$licdr['canal']]] = 1;
    }
    return $licdr;
}

function GenRq(&$tb, $tbc, $newEvent = 0) {
    // Generation Req Attribut
    global $allRq, $tbMSISDN, $cdrsFile, $tbAllTables, $allRqAttr, $rep_log, $config, $new_mechili, $old_mechili;
    $dj = substr($tb['heure'], 0, 8);
    $dm = substr($tb['heure'], 0, 6);
    $da = substr($tb['heure'], 0, 4);
    $tmCdr = $tb['heure'];
    $MSISDN = $tb['msisdn'];
    $config['mechili'] = ($dj > '20170604') ? $new_mechili : $old_mechili;

    if (!in_array($tb['msisdn'], $tbMSISDN))
        $tbMSISDN[] = $tb['msisdn'];

    $tabChTot = $tabChNbr = array();
    if (isset($tbc[$dj])) {
        $nmCh = $tbc[$dj];
        $tabChTot[$nmCh] = $tb['valeur'];
        $tabChNbr[$nmCh] = 1;
    }

    if (isset($tbc[$dm])) {
        $nmCh = $tbc[$dm];
        $tabChTot[$nmCh] = $tb['valeur'];
        $tabChNbr[$nmCh] = 1;
    }
    if (isset($tbc[$da])) {
        $nmCh = $tbc[$da];
        $tabChTot[$nmCh] = $tb['valeur'];
        $tabChNbr[$nmCh] = 1;
    }
    if (isset($config['canal'][$tb['canal']]))
        $tb['canalrecharge'] = $config['canal'][$tb['canal']];
    elseif (isset($config['access'][$tb['canal']]))
        $tb['canalrecharge'] = strtolower($config['access'][$tb['canal']]);
    else
        $tb['canalrecharge'] = '0';
    $tb['consommation'] = 0;
//    $tb['canalrecharge'] = isset($config['canal'][$tb['canal']]) ? $config['canal'][$tb['canal']] : (isset($config['access'][$tb['canal']]) ? strtolower($config['access'][$tb['canal']]) : '0');
    if ($tb['canal']) { //Recharge
        foreach ($tb['prefix'] as $prfx => $val) {
            if ($prfx == "total" && !$newEvent) { //Tous type de recharge (mechili, facial, ws et autre)
                fn_gen_rq("data_recharge_valeur_$prfx", $MSISDN, $tabChTot);
                fn_gen_rq("data_recharge_nombre_$prfx", $MSISDN, $tabChNbr);
            }

            if (isset($config['access'][$tb['access']])) {
                if ($config['access'][$tb['access']] == 'ValeurFacial') {
                    // Total sur le canal de recharge facial
                    if (!$newEvent) {
                        fn_gen_rq("data_recharge_valeur_" . $prfx . "_total", $MSISDN, $tabChTot);
                        fn_gen_rq("data_recharge_nombre_" . $prfx . "_total", $MSISDN, $tabChNbr);
                    }

                    // détail par valeur facial
                    // Vérifier si la table existe ou non
                    if (in_array('data_recharge_nombre_' . $prfx . '_' . $tb['val_facial'], $tbAllTables)) {
                        fn_gen_rq('data_recharge_nombre_' . $prfx . '_' . $tb['val_facial'], $MSISDN, $tabChNbr);
                        fn_gen_rq('data_recharge_valeur_' . $prfx . '_' . $tb['val_facial'], $MSISDN, $tabChTot);
                    } else {
                        $fo = fopen($rep_log . "log_vou_facial_" . $tb['val_facial'], 'a');
                        fputs($fo, json_encode($tb) . "\r\n");
                        fclose($fo);
                    }
                } elseif ($config['access'][$tb['access']] == 'Mechili') {
                    if (!$newEvent) {
                        fn_gen_rq("data_recharge_valeur_mechili_total", $MSISDN, $tabChTot);
                        fn_gen_rq("data_recharge_nombre_mechili_total", $MSISDN, $tabChNbr);
                    }
                    $plage = "";
                    foreach ($config['mechili'] as $key => $value) {
                        if ($tb['val_facial'] <= $value['sup'] && $tb['val_facial'] >= $value['inf']) {
                            $plage = $key;
                            break;
                        }
                    }
                    if ($plage != "") {
                        $tb['plage'] = $plage;
                        fn_gen_rq("data_recharge_valeur_mechili_" . $plage, $MSISDN, $tabChTot);
                        fn_gen_rq("data_recharge_nombre_mechili_" . $plage, $MSISDN, $tabChNbr);
                    } else { // Montant mechili non declaré
                        $fo = fopen($rep_log . "log_vou_mechili", 'a');
                        fputs($fo, json_encode($tb) . "\r\n");
                        fclose($fo);
                    }
                } elseif ($config['access'][$tb['access']] == 'WebSevices') {
                    fn_gen_rq("data_recharge_valeur_webservice", $MSISDN, $tabChTot);
                    fn_gen_rq("data_recharge_nombre_webservice", $MSISDN, $tabChNbr);
                } else {
                    fn_gen_rq("data_recharge_valeur_autre", $MSISDN, $tabChTot);
                    fn_gen_rq("data_recharge_nombre_autre", $MSISDN, $tabChNbr);
                }
            } else { // Ecrire dans un fichier de log
                $fo = fopen($rep_log . "log_vou_access_" . $tb['access'], 'a');
                fputs($fo, json_encode($tb) . "\r\n");
                fclose($fo);
            }
        }
        if (!$newEvent && (!isset($allRqAttr['recharge'][$MSISDN]['heure']) || $allRqAttr['recharge'][$MSISDN]['heure'] < $tmCdr)) {
            $allRqAttr['recharge'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['recharge'][$MSISDN]['req_val'] = $tb['balance']
                    . '||' . $tb['profil']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['dt_active_stop']
                    . '||' . $tb['dt_suspend_stop']
                    . '||' . $tb['dt_disable_stop']
                    . '||' . $tb['valeur'];
        }
    } else {
        if (!isset($allRqAttr['activation'][$MSISDN]['heure']) || $allRqAttr['activation'][$MSISDN]['heure'] < $tmCdr) {
            $allRqAttr['activation'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['activation'][$MSISDN]['req_val'] = $tb['balance']
                    . '||' . $tb['profil']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['dt_active_stop']
                    . '||' . $tb['dt_suspend_stop']
                    . '||' . $tb['dt_disable_stop']
                    . '||' . $tb['cellid'];
        }
    }
}

function execute_attribut($tbMSISDN, $allRqAttr, $connection) {
    try {
        if (count($tbMSISDN)) {
            $chaineMSISDN = "'" . implode("','", $tbMSISDN) . "'";
            $tbNtMSISDN = $tbAttrMSISDN = $tbAttrMSISDNdate = array();

            $reqVerifNum = 'SELECT numero, dt_recharge, dt_profil, dt_status, dt_active_stop, 
		dt_suspend_stop, dt_disable_stop, dt_balance, dt_active, lieu_activation FROM data_attribut WHERE numero in (' . $chaineMSISDN . ')';

            $result = $connection->query($reqVerifNum);
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//            $tbAttrMSISDN[] = $ligne->numero;
                $tbAttrMSISDNdate[$ligne->numero]['dt_active'] = $ligne->dt_active;
                $tbAttrMSISDNdate[$ligne->numero]['dt_recharge'] = $ligne->dt_recharge;
                $tbAttrMSISDNdate[$ligne->numero]['dt_balance'] = $ligne->dt_balance;
                $tbAttrMSISDNdate[$ligne->numero]['dt_profil'] = $ligne->dt_profil;
                $tbAttrMSISDNdate[$ligne->numero]['dt_status'] = $ligne->dt_status;
                $tbAttrMSISDNdate[$ligne->numero]['dt_active_stop'] = $ligne->dt_active_stop;
                $tbAttrMSISDNdate[$ligne->numero]['dt_suspend_stop'] = $ligne->dt_suspend_stop;
                $tbAttrMSISDNdate[$ligne->numero]['dt_disable_stop'] = $ligne->dt_disable_stop;
                $tbAttrMSISDNdate[$ligne->numero]['lieu_activation'] = $ligne->lieu_activation;
            }

            // ********* update des tables data
            //#####################################################################

            if (isset($allRqAttr['activation'])) {
                foreach ($allRqAttr['activation'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {
                        if ($tbAttrMSISDNdate[$ms]['dt_balance'] < $tb['heure'])
                            $cnd[] = "dt_balance = '" . $tb['heure'] . "', balance = " . $ChVals[0];
                        if ($tbAttrMSISDNdate[$ms]['dt_profil'] < $tb['heure'])
                            $cnd[] = "dt_profil = '" . $tb['heure'] . "', profil = " . $ChVals[1];
                        if ($tbAttrMSISDNdate[$ms]['dt_status'] < $tb['heure'])
                            $cnd[] = "dt_status = '" . $tb['heure'] . "', status = " . $ChVals[2];
                        if ($tbAttrMSISDNdate[$ms]['dt_active'] < $tb['heure']) {
                            $cnd[] = "dt_active = '" . $tb['heure'] . "', lieu_activation = '" . $ChVals[6] . "'";
                            $cnd[] = "dt_active_stop = '" . $ChVals[3] . "'";
                            $cnd[] = "dt_suspend_stop = '" . $ChVals[4] . "'";
                            $cnd[] = "dt_disable_stop = '" . $ChVals[5] . "'";
                        }
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    } else {
                        $req = "INSERT INTO data_attribut (numero, dt_balance, balance, dt_profil, profil, dt_status, status, dt_active, 
                        dt_active_stop, dt_suspend_stop, dt_disable_stop, lieu_activation) 
                        VALUES ('" . $ms . "', '" . $tb['heure'] . "', " . $ChVals[0] . ", '" . $tb['heure'] . "'," . $ChVals[1] . ",
				'" . $tb['heure'] . "', " . $ChVals[2] . ", '" . $tb['heure'] . "', '" . $ChVals[3] . "', '" . $ChVals[4] . "', '" . $ChVals[5] . "', '" . $ChVals[6] . "')";

                        $tbAttrMSISDNdate[$ms]['dt_active'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_recharge'] = '';
                        $tbAttrMSISDNdate[$ms]['dt_balance'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_profil'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_status'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_active_stop'] = $ChVals[3];
                        $tbAttrMSISDNdate[$ms]['dt_suspend_stop'] = $ChVals[4];
                        $tbAttrMSISDNdate[$ms]['dt_disable_stop'] = $ChVals[5];
                    }
                    if ($req != '') {
//                        echo "\r\n <br>" . $req . ';';
                        $result = $connection->query($req);
                    }
                }
            }

            if (isset($allRqAttr['recharge'])) {
                foreach ($allRqAttr['recharge'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {
                        if ($tbAttrMSISDNdate[$ms]['dt_balance'] < $tb['heure'])
                            $cnd[] = "dt_balance = '" . $tb['heure'] . "', balance = " . $ChVals[0];
                        if ($tbAttrMSISDNdate[$ms]['dt_profil'] < $tb['heure'])
                            $cnd[] = "dt_profil = '" . $tb['heure'] . "', profil = " . $ChVals[1];
                        if ($tbAttrMSISDNdate[$ms]['dt_status'] < $tb['heure'])
                            $cnd[] = "dt_status = '" . $tb['heure'] . "', status = " . $ChVals[2];
                        if ($tbAttrMSISDNdate[$ms]['dt_active_stop'] < $ChVals[3])
                            $cnd[] = "dt_active_stop = '" . $ChVals[3] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_suspend_stop'] < $ChVals[4])
                            $cnd[] = "dt_suspend_stop = '" . $ChVals[4] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_disable_stop'] < $ChVals[5])
                            $cnd[] = "dt_disable_stop = '" . $ChVals[5] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_recharge'] < $tb['heure'])
                            $cnd[] = "dt_recharge = '" . $tb['heure'] . "', montant_recharge = '" . $ChVals[6] . "'";
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    } else {
                        $req = "INSERT INTO data_attribut (numero, dt_balance, balance, dt_profil, profil, dt_status, status, dt_recharge,
                        dt_active_stop, dt_suspend_stop, dt_disable_stop, montant_recharge) 
                        VALUES ('" . $ms . "', '" . $tb['heure'] . "',  " . $ChVals[0] . ", '" . $tb['heure'] . "', " . $ChVals[1] . ",
				'" . $tb['heure'] . "', " . $ChVals[2] . ", '" . $tb['heure'] . "', '" . $ChVals[3] . "', 
                                '" . $ChVals[4] . "', '" . $ChVals[5] . "', '" . $ChVals[6] . "')";
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