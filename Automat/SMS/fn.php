<?php

function GenInfosCdr($licdr = array(), $config) {
    global $rep_log_ignored;
    if ($licdr['smsid'] == 1)
        return $licdr;
    $licdr['msisdn'] = verifierNumero($licdr['msisdn'], $config);
    $licdr['msisdn_autre'] = verifierNumero($licdr['msisdn_autre'], $config);
    $licdr['msc'] = verifierNumero($licdr['msc'], $config);

    $licdr['imsi_correcte'] = 0;
    if (strlen($licdr['imsi']) == $config['ln_imsi'] && substr($licdr['imsi'], 0, strlen($config['network_code'])) == $config['network_code'])
        $licdr['imsi_correcte'] = 1;

    $substr_msisdn = substr($licdr['msisdn'], 0, strlen($config['code_op']));
    $substr_msc = substr($licdr['msc'], 0, strlen($config['code_op']));
    $substr_autre = substr($licdr['msisdn_autre'], 0, strlen($config['code_op']));
    if ($licdr['type_sms'] == 1 &&
            strlen($licdr['msisdn']) == $config['ln_int'] &&
            $substr_msisdn == $config['code_op']
    ) {
        $licdr['considere'] = 1;
        $licdr['prefix']['total'] = 1;
        if ($substr_msc == $config['code_op']) { //Dans le pays
            $licdr['prefix']['pays'] = 1;
            if ($substr_autre == $config['code_op'] && strlen($licdr['msisdn_autre']) == $config['ln_int'])
                $licdr['prefix']['onnet'] = 1;
            elseif (in_array($substr_autre, $config['offnet']) && strlen($licdr['msisdn_autre']) == $config['ln_int'])
                $licdr['prefix']['offnet'] = 1;
            elseif (strlen($licdr['msisdn_autre']) > $config['ln_nat'])
                $licdr['prefix']['int'] = 1;
            else
                $licdr['prefix']['spe'] = 1;
        }
        else
            $licdr['prefix']['roa'] = 1;
    }elseif ($licdr['type_sms'] == 2 &&
            strlen($licdr['msisdn_autre']) == $config['ln_int'] &&
            $substr_autre == $config['code_op']) {
        $msisdn = $licdr['msisdn'];
        $licdr['msisdn'] = $licdr['msisdn_autre'];
        $licdr['msisdn_autre'] = $msisdn;
        $licdr['considere'] = 1;
        $licdr['prefix']['total'] = 1;
        if ($licdr['RoamNetworkCode'] != $config['network_code']) { // SMS roaming
            $licdr['prefix']['roa'] = 1;
        } else {
            $licdr['prefix']['pays'] = 1;
        }
    } else {
        if ($licdr['type_sms'] == 1 || $licdr['type_sms'] == 2)
            $licdr['cause'] = 'MSISDN non valide';
        else
            $licdr['cause'] = 'Type SMS non valide';
        $fo = fopen($rep_log_ignored . "SMS_" . $fileName, 'a');
        fputs($fo, json_encode($licdr) . "\r\n");
        fclose($fo);
        return $licdr;
    }
    return $licdr;
}

function GenRq(&$tb, $tbc) {
    // Generation Req Attribut
    global $allRq, $tbMSISDN, $allRqAttr, $config, $tbAllTables;
    $tabChV = $tabChN = array();
    $tmCdr = $tb['heure'];
    $MSISDN = $tb['msisdn'];
    $dj = substr($tmCdr, 0, 8);
    $dm = substr($tmCdr, 0, 6);
    $da = substr($tmCdr, 0, 4);

    if (!in_array($MSISDN, $tbMSISDN))
        $tbMSISDN[] = $MSISDN;
    $tb['allmonnaie'] = 0;
    $tb['allsms'] = 0;
    if (isset($config['agreg'][$tb['compteur']]))
        $tb[$config['agreg'][$tb['compteur']]] = $tb['cout'];

    $principal = ($tb['compteur'] == 2000) ? true : false;
    //$tb['cout'] = $tb['cout'] + $tb['tva'];      
    if ($tb['oper_type'])
        $tb['cout'] = 0;
    if (isset($tbc[$dj])) {
        $nmCh = $tbc[$dj];
        $tabChN[$nmCh] = 1;
        $tabChV[$nmCh] = $tb['cout'];
    }
    if (isset($tbc[$dm])) {
        $nmCh = $tbc[$dm];
        $tabChN[$nmCh] = 1;
        $tabChV[$nmCh] = $tb['cout'];
    }
    if (isset($tbc[$da])) {
        $nmCh = $tbc[$da];
        $tabChN[$nmCh] = 1;
        $tabChV[$nmCh] = $tb['cout'];
    }
    if ($tb['type_sms'] == 1) { // SMS émis
        foreach ($tb['prefix'] as $prfx => $val) {
            fn_gen_rq("data_sms_emis_nombre_$prfx", $MSISDN, $tabChN);

            if ($principal) {
                fn_gen_rq("data_sms_emis_valeur_$prfx", $MSISDN, $tabChV);
            }
        }

        if ($principal) {
            $tb['consommation'] = $tb['cout'];
            fn_gen_rq("data_consommation_total", $MSISDN, $tabChV);
        }
        else
            $tb['consommation'] = 0;
        if ($tb['cout']) {
            if (!in_array('data_sms_emis_valeur_' . $tb['compteur'], $tbAllTables) || !isset($config['agreg'][$tb['compteur']])) {
                $fo = fopen($rep_log . "log_cmp_sms_" . $tb['compteur'], 'a');
                fputs($fo, json_encode($tb) . "\r\n");
                fclose($fo);
            } else {
                fn_gen_rq("data_sms_emis_valeur_" . $tb['compteur'], $MSISDN, $tabChV);
                fn_gen_rq("data_sms_emis_valeur_" . $config['agreg'][$tb['compteur']], $MSISDN, $tabChV);
            }
        }
    } else {    // SMS reçu
        if ($tb['cout']) {
            foreach ($tb['prefix'] as $prfx => $val) {
                fn_gen_rq("data_sms_recu_valeur_$prfx", $MSISDN, $tabChV);
            }
            fn_gen_rq("data_consommation_total", $MSISDN, $tabChV);
            $tb['consommation'] = $tb['cout'];
        }else
            $tb['consommation'] = 0;
        if (isset($tb['prefix']['roa'])) {
            fn_gen_rq("data_sms_recu_nombre_total", $MSISDN, $tabChN);
            fn_gen_rq("data_sms_recu_nombre_roa", $MSISDN, $tabChN);
        }
    }
    $tp_sms = ($tb['type_sms'] == 2) ? 'recu' : 'emis';
    if (!isset($allRqAttr[$tp_sms][$MSISDN]['heure']) || $allRqAttr[$tp_sms][$MSISDN]['heure'] < $tmCdr) {
        $allRqAttr[$tp_sms][$MSISDN]['heure'] = $tmCdr;
        $allRqAttr[$tp_sms][$MSISDN]['req_val'] = $tb['balance']
                . '||' . $tb['profil']
                . '||' . substr($tb['status'], 0, 1)
                . '||' . $tb['imsi']
                . '||' . $tb['imsi_correcte'];
    }

    $tb['compteur'] = array($tb['compteur'] => $tb['cout']);
}

function execute_attribut($tbMSISDN, $allRqAttr, $connection) {
    try {
        if (count($tbMSISDN)) {
            $chaineMSISDN = "'" . implode("','", $tbMSISDN) . "'";
            $tbNtMSISDN = $tbAttrMSISDN = $tbAttrMSISDNdate = array();
            $reqVerifNum = 'SELECT numero, dt_sms, dt_sms_recu, dt_profil, dt_balance, dt_imsi, dt_status FROM data_attribut WHERE numero in (' . $chaineMSISDN . ')';
            $result = $connection->query($reqVerifNum);
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//            $tbAttrMSISDN[] = $ligne->numero;
                $tbAttrMSISDNdate[$ligne->numero]['dt_sms'] = $ligne->dt_sms;
                $tbAttrMSISDNdate[$ligne->numero]['dt_sms_recu'] = $ligne->dt_sms_recu;
                $tbAttrMSISDNdate[$ligne->numero]['dt_balance'] = $ligne->dt_balance;
                $tbAttrMSISDNdate[$ligne->numero]['dt_profil'] = $ligne->dt_profil;
                $tbAttrMSISDNdate[$ligne->numero]['dt_status'] = $ligne->dt_status;
                $tbAttrMSISDNdate[$ligne->numero]['dt_imsi'] = $ligne->dt_imsi;
            }

            //#####################################################################
            // ********* update de la table attr
            if (isset($allRqAttr['emis'])) {
                foreach ($allRqAttr['emis'] as $ms => $tb) {
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
                        if ($tbAttrMSISDNdate[$ms]['dt_imsi'] < $tb['heure'] && $ChVals[4])
                            $cnd[] = "dt_imsi = '" . $tb['heure'] . "', imsi = '" . $ChVals[3] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_sms'] < $tb['heure'])
                            $cnd[] = "dt_sms = '" . $tb['heure'] . "'";
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $updImsi = ($ChVals[4]) ? "'" . $tb['heure'] . "', '" . $ChVals[3] . "'" : 'NULL, NULL';
                        $req = "INSERT INTO data_attribut (numero, dt_balance, balance, dt_profil, profil, dt_status, status, 
                        dt_sms, dt_imsi, imsi) VALUES  
                        ('" . $ms . "','" . $tb['heure'] . "'," . $ChVals[0] . ",'" . $tb['heure'] . "', " . $ChVals[1] . ",
			'" . $tb['heure'] . "',  " . $ChVals[2] . ",'" . $tb['heure'] . "', " . $updImsi . ")";

                        $tbAttrMSISDNdate[$ms]['dt_sms'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_sms_recu'] = '';
                        $tbAttrMSISDNdate[$ms]['dt_balance'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_profil'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_status'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_imsi'] = $tb['heure'];
                    }
                    if ($req != '') {
//                    echo "\r\n <br>" . $req . ';';
                        $result = $connection->query($req);
                    }
                }
            }

            if (isset($allRqAttr['recu'])) {
                foreach ($allRqAttr['recu'] as $ms => $tb) {
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
                        if ($tbAttrMSISDNdate[$ms]['dt_imsi'] < $tb['heure'] && $ChVals[4])
                            $cnd[] = "dt_imsi = '" . $tb['heure'] . "', imsi = '" . $ChVals[3] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_sms_recu'] < $tb['heure'])
                            $cnd[] = "dt_sms_recu = '" . $tb['heure'] . "'";
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $updImsi = ($ChVals[4]) ? "'" . $tb['heure'] . "', '" . $ChVals[3] . "'" : ' NULL, NULL';
                        $req = "INSERT INTO data_attribut (numero, dt_balance, balance, dt_profil, profil, dt_status, status, dt_sms_recu, dt_imsi, imsi)
                        VALUES ('" . $ms . "', '" . $tb['heure'] . "', " . $ChVals[0] . ", '" . $tb['heure'] . "', " . $ChVals[1] . ",
				'" . $tb['heure'] . "', " . $ChVals[2] . ",'" . $tb['heure'] . "', " . $updImsi . ")";
                    }
                    if ($req != '') {
//                     echo "\r\n <br>" . $req . ';';
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