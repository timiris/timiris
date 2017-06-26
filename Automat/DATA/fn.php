<?php

function GenInfosCdr($licdr = array(), $config, $fileName) {
    global $rep_log_ignored;
    $licdr['msisdn'] = verifierNumero($licdr['msisdn'], $config);
    if ($licdr['profil'] == '333333') {
//        $fo = fopen($rep_log_ignored . "DATA_" . $fileName, 'a');
//        $licdr['cause'] = 'MSISDN postpaid';
//        fputs($fo, json_encode($licdr) . "\r\n");
//        fclose($fo);
        return $licdr;
    }

    if ($licdr['ResultCode'] != 0) {
//        $fo = fopen($rep_log_ignored . "DATA_" . $fileName, 'a');
//        $licdr['cause'] = 'Anomalie OCS, Code : '.$licdr['ResultCode'];
//        fputs($fo, json_encode($licdr) . "\r\n");
//        fclose($fo);
        return $licdr;
    }

    if ($licdr['volume'] != 0 && strlen($licdr['msisdn']) == $config['ln_int'] &&
            substr($licdr['msisdn'], 0, strlen($config['code_op'])) == $config['code_op']
    ) {
        $licdr['considere'] = 1;
        $licdr['prefix']['total'] = 1;
        if ($licdr['RoamState'] == 1)
            $licdr['prefix']['roa'] = 1;
        else
            $licdr['prefix']['pays'] = 1;
    }elseif ($licdr['volume'] != 0) {
        $fo = fopen($rep_log_ignored . "DATA_" . $fileName, 'a');
        $licdr['cause'] = 'MSISDN non valide';
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

    $tabChVo = $tabChVa = array();
    if (!in_array($MSISDN, $tbMSISDN))
        $tbMSISDN[] = $MSISDN;

    if (isset($tbc[$dj])) {
        $nmCh = $tbc[$dj];
        $tabCh[] = $nmCh;
        $tabChVo[$nmCh] = $tb['volume'];
    }

    if (isset($tbc[$dm])) {
        $nmCh = $tbc[$dm];
        $tabCh[] = $nmCh;
        $tabChVo[$nmCh] = $tb['volume'];
    }
    if (isset($tbc[$da])) {
        $nmCh = $tbc[$da];
        $tabCh[] = $nmCh;
        $tabChVo[$nmCh] = $tb['volume'];
    }

    $compteur = array();
    $tb['allmonnaie'] = 0;
    $tb['alldata'] = 0;
    $i = 1;
    while ($i <= 10 && $tb['compteur' . $i] && $tb['cout' . $i] && !$tb['oper_type' . $i]) {
        $tb[$config['agreg'][$tb['compteur' . $i]]] += $tb['cout' . $i];
        if (!in_array('data_data_valeur_' . $tb['compteur' . $i], $tbAllTables)) {
            $fo = fopen($rep_log . "log_data_cmp_" . $tb['compteur' . $i], 'a');
            fputs($fo, json_encode($tb) . "\r\n");
            fclose($fo);
        } else {
            if (isset($compteur[$tb['compteur' . $i]]))
                $compteur[$tb['compteur' . $i]] += $tb['cout' . $i];
            else
                $compteur[$tb['compteur' . $i]] = $tb['cout' . $i];
        }
        $i++;
    }
    $tb['compteur'] = $compteur;
    $tbUpd_pri = array();
    if (isset($compteur['2000'])) {
        $tb['consommation'] = $compteur['2000'];
        foreach ($tabCh as $ch) {
            $tbUpd_pri[$ch] = $compteur['2000'];
        }
    }else
        $tb['consommation'] = 0;
    foreach ($tb['prefix'] as $prfx => $val) {
        fn_gen_rq("data_data_volume_$prfx", $MSISDN, $tabChVo);
        if (count($tbUpd_pri)) { // pour les préfixes (roa, ds_pays et total) seulement le montant déduit du compteur principal
            fn_gen_rq("data_data_valeur_$prfx", $MSISDN, $tbUpd_pri);
            if ($prfx == 'total')
                fn_gen_rq("data_consommation_total", $MSISDN, $tbUpd_pri);
        }
    }

    foreach ($compteur as $key => $val) {   // Boucle sur les compteurs dans le cdr pour maj des tables valeurs correspondates
        if ($val != 0) {
            $tbUpd = array();
            foreach ($tabCh as $ch) {
                $tbUpd[$ch] = $val;
            }
            if (count($tbUpd)) {
                fn_gen_rq("data_data_valeur_$key", $MSISDN, $tbUpd);
                //mise a jours des agrégations de valeur pour ce compteur (data, monnaie)
                if (isset($config['agreg'][$key])) {
                    fn_gen_rq("data_data_valeur_" . $config['agreg'][$key], $MSISDN, $tbUpd);
                }
            }
        }
    }

    if (!isset($allRqAttr[$MSISDN]['heure']) || $allRqAttr[$MSISDN]['heure'] < $tmCdr) {
        $allRqAttr[$MSISDN]['heure'] = $tmCdr;
        $allRqAttr[$MSISDN]['req_val'] = $tb['balance'] . '||' . $tb['profil'] . '||' . substr($tb['status'], 0, 1);
    }
}

function execute_attribut($tbMSISDN, $allRqAttr, $connection) {
    try {
        if (count($tbMSISDN)) {
            $chaineMSISDN = "'" . implode("','", $tbMSISDN) . "'";
            $tbAttrMSISDNdate = array();

            $reqVerifNum = 'SELECT numero, dt_data, dt_profil, dt_balance, dt_status FROM data_attribut WHERE numero in (' . $chaineMSISDN . ')';

            $result = $connection->query($reqVerifNum);
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                //$tbAttrMSISDN[] = $ligne->numero;
                $tbAttrMSISDNdate[$ligne->numero]['dt_data'] = $ligne->dt_data;
                $tbAttrMSISDNdate[$ligne->numero]['dt_profil'] = $ligne->dt_profil;
                $tbAttrMSISDNdate[$ligne->numero]['dt_balance'] = $ligne->dt_balance;
                $tbAttrMSISDNdate[$ligne->numero]['dt_status'] = $ligne->dt_status;
            }

            // ********* update de la table attr
            if (isset($allRqAttr)) {
                foreach ($allRqAttr as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {
                        if ($tbAttrMSISDNdate[$ms]['dt_data'] < $tb['heure'])
                            $cnd[] = "dt_data = '" . $tb['heure'] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_balance'] < $tb['heure'])
                            $cnd[] = "dt_balance = '" . $tb['heure'] . "', balance = '" . $ChVals[0] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_profil'] < $tb['heure'])
                            $cnd[] = "dt_profil = '" . $tb['heure'] . "', profil = '" . $ChVals[1] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_status'] < $tb['heure'])
                            $cnd[] = "dt_status = '" . $tb['heure'] . "', status = '" . $ChVals[2] . "'";
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $req = "INSERT INTO data_attribut (numero, balance, dt_balance, profil, dt_profil, status, dt_status, dt_data)
                            VALUES 
                        ('$ms', '" . $ChVals[0] . "', '" . $tb['heure'] . "', '" . $ChVals[1] . "', '" . $tb['heure'] . "',
                            '" . $ChVals[2] . "', '" . $tb['heure'] . "', '" . $tb['heure'] . "' )";
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