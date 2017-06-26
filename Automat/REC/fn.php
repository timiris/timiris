<?php

function GenInfosCdr($licdr = array(), $config, $fileName) {
    global $rep_log_ignored;
    if ($licdr['CallForwardIndicator'] != '00')
        $licdr['msisdn'] = $licdr['msisdn_renvoi'];
    $licdr['msc1'] = verifierNumero($licdr['msc1'], $config);
    $licdr['msc'] = verifierNumero($licdr['msc'], $config);
    $licdr['msisdn'] = verifierNumero($licdr['msisdn'], $config);
    $licdr['msisdn_appele'] = verifierNumero($licdr['msisdn_appele'], $config);
    $licdr['imsi_correcte'] = 0;
    if (strlen($licdr['imsi']) == $config['ln_imsi'] && substr($licdr['imsi'], 0, strlen($config['network_code'])) == $config['network_code'])
        $licdr['imsi_correcte'] = 1;
    if ($licdr['type_appel'] == 2) {
        if (trim($licdr['msc1']) == '' || substr($licdr['msc1'], 0, strlen($config['code_op'])) == $config['code_op']) {
            $fo = fopen($rep_log_ignored . "REC_" . $fileName, 'a');
            $licdr['cause'] = 'MSC de l\'appelé non valide';
            fputs($fo, json_encode($licdr) . "\r\n");
            fclose($fo);
            return $licdr;
        }
        if (strlen($licdr['msisdn_appele']) == $config['ln_int'] && substr($licdr['msisdn_appele'], 0, strlen($config['code_op'])) == $config['code_op']
        ) {
            $msisdn = $licdr['msisdn'];
            $licdr['msisdn'] = $licdr['msisdn_appele'];
            $licdr['msisdn_appele'] = $msisdn;
            $licdr['msc'] = $licdr['msc1'];
            $licdr['imsi'] = $licdr['imsi1'];
            $licdr['cellid'] = $licdr['cellid1'];
            $licdr['prefix']['roa'] = 1;
            $licdr['prefix']['total'] = 1;
            $licdr['considere'] = 1;
            return $licdr;
        } else {
            $fo = fopen($rep_log_ignored . "REC_" . $fileName, 'a');
            $licdr['cause'] = 'MSISDN appelé non valide';
            fputs($fo, json_encode($licdr) . "\r\n");
            fclose($fo);
            return $licdr;
        }
    } elseif ($licdr['type_appel'] == 1) {
        if (strlen($licdr['msisdn']) == $config['ln_int'] && substr($licdr['msisdn'], 0, strlen($config['code_op'])) == $config['code_op']
        ) {
            $licdr['considere'] = 1;
            $licdr['prefix']['total'] = 1;

            if (substr($licdr['msc'], 0, strlen($config['code_op'])) == $config['code_op'] && strlen($licdr['msc']) == $config['ln_int']) { // dans le pays
                $licdr['prefix']['pays'] = 1;
                if (strlen($licdr['msisdn_appele']) < $config['ln_nat']) {
                    $licdr['prefix']['spe'] = 1;
                } elseif (substr($licdr['msisdn_appele'], 0, strlen($config['code_op'])) == $config['code_op'] && strlen($licdr['msisdn_appele']) == $config['ln_int']) {
                    $licdr['prefix']['onnet'] = 1;
                } elseif (substr($licdr['msisdn_appele'], 0, strlen($config['code_pays'])) == $config['code_pays'] && strlen($licdr['msisdn_appele']) == $config['ln_int']) {
                    $licdr['prefix']['offnet'] = 1;
                } else {
                    $licdr['prefix']['int'] = 1;
                    if (isset($config['pays'][substr($licdr['msisdn_appele'], 0, 2)]))
                        $licdr['pays_dest'] = $config['pays'][substr($licdr['msisdn_appele'], 0, 2)];
                    if (isset($config['pays'][substr($licdr['msisdn_appele'], 0, 3)]))
                        $licdr['pays_dest'] = $config['pays'][substr($licdr['msisdn_appele'], 0, 3)];
                }
            }
            else
                $licdr['prefix']['roa'] = 1;
            $licdr['gratuit'] = 0;
            if ($licdr['cout1'] == 0)
                $licdr['gratuit'] = 1;
            return $licdr;
        }else {
            $fo = fopen($rep_log_ignored . "REC_" . $fileName, 'a');
            $licdr['cause'] = 'MSISDN appelant non valide';
            fputs($fo, json_encode($licdr) . "\r\n");
            fclose($fo);
            return $licdr;
        }
    }
    return $licdr;
}

function GenRq(&$tb, $tbc) {
    // Generation Req Attribut
    $MSISDN = $tb['msisdn'];
    $tmCdr = $tb['heure'];

    global $allRq, $tbMSISDN, $cdrsFile, $tbAllTables, $rep_log, $allRqAttr, $config;
    $dj = substr($tb['heure'], 0, 8);
    $dm = substr($tb['heure'], 0, 6);
    $da = substr($tb['heure'], 0, 4);
    $tabChNbr = $tabCh = $tabChDur = array();
    if (!in_array($tb['msisdn'], $tbMSISDN))
        $tbMSISDN[] = $tb['msisdn'];
    if (isset($tbc[$dj])) {
        $nmCh = $tbc[$dj];
        $tabCh[] = $nmCh;
        $tabChNbr[$nmCh] = 1;
        $tabChDur[$nmCh] = $tb['duree'];
    }
    if (isset($tbc[$dm])) {
        $nmCh = $tbc[$dm];
        $tabCh[] = $nmCh;
        $tabChNbr[$nmCh] = 1;
        $tabChDur[$nmCh] = $tb['duree'];
    }
    if (isset($tbc[$da])) {
        $nmCh = $tbc[$da];
        $tabCh[] = $nmCh;
        $tabChNbr[$nmCh] = 1;
        $tabChDur[$nmCh] = $tb['duree'];
    }

    $compteur = array();
    $tb['allmonnaie'] = 0;
    $tb['alltime'] = 0;
    $i = 1;
    while ($i <= 10 && $tb['compteur' . $i] && $tb['cout' . $i] && !$tb['oper_type' . $i]) {
        $tb[$config['agreg'][$tb['compteur' . $i]]] += $tb['cout' . $i];
        if (!in_array('data_appel_emis_valeur_' . $tb['compteur' . $i], $tbAllTables)) {
            $fo = fopen($rep_log . "log_rec_cmp_" . $tb['compteur' . $i], 'a');
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
    $tbUpd_pri = array();
    if (isset($compteur['2000'])) {
        $tb['consommation'] = $compteur['2000'];
        foreach ($tabCh as $ch) {
            $tbUpd_pri[$ch] = $compteur['2000'];
        }
    }
    else
        $tb['consommation'] = 0;
    $tb['compteur'] = $compteur;
    if ($tb['type_appel'] == 2) { // Appel recu en roaming
        if (count($tbUpd_pri)) {
            fn_gen_rq("data_appel_recu_valeur_roa", $MSISDN, $tbUpd_pri);
            fn_gen_rq("data_appel_recu_valeur_total", $MSISDN, $tbUpd_pri);
            fn_gen_rq("data_consommation_total", $MSISDN, $tbUpd_pri);
        }

        foreach ($tb['prefix'] as $prfx => $val) {
            fn_gen_rq("data_appel_recu_nombre_$prfx", $MSISDN, $tabChNbr);
            fn_gen_rq("data_appel_recu_duree_$prfx", $MSISDN, $tabChDur);
        }

        if (!isset($allRqAttr['recu'][$MSISDN]['heure']) || $allRqAttr['recu'][$MSISDN]['heure'] < $tmCdr) {
            $allRqAttr['recu'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['recu'][$MSISDN]['req_val'] = (int) $tb['balance']
                    . '||' . $tb['profil']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['imsi']
                    . '||' . $tb['imsi_correcte']
                    . '||' . $tb['msc']
                    . '||' . $tb['cellid']
                    . '||' . implode('::', array_keys($tb['prefix']));
        }
    } else { // Appel emis
        foreach ($tb['prefix'] as $prfx => $val) {
            fn_gen_rq("data_appel_emis_nombre_$prfx", $MSISDN, $tabChNbr);
            fn_gen_rq("data_appel_emis_duree_$prfx", $MSISDN, $tabChDur);
            if (count($tbUpd_pri)) {
                fn_gen_rq("data_appel_emis_valeur_$prfx", $MSISDN, $tbUpd_pri);
            }
        }

        if (count($tbUpd_pri)) {
            fn_gen_rq("data_consommation_total", $MSISDN, $tbUpd_pri);
        }

        if (isset($tb['pays_dest'])) {
            $prfx = $tb['pays_dest'];
            fn_gen_rq("data_appel_emis_nombre_$prfx", $MSISDN, $tabChNbr);
            fn_gen_rq("data_appel_emis_duree_$prfx", $MSISDN, $tabChDur);

            if (count($tbUpd_pri)) {
                fn_gen_rq("data_appel_emis_valeur_$prfx", $MSISDN, $tbUpd_pri);
            }
        }
        //****

        foreach ($compteur as $key => $val) {
            if ($val != 0) {
                $tbUpd = array();
                foreach ($tabCh as $ch) {
                    $tbUpd[$ch] = $val;
                }
                if (count($tbUpd)) {
                    fn_gen_rq("data_appel_emis_valeur_$key", $MSISDN, $tbUpd);
                    //mise a jours des agrégations (monnaie, time) de valeur pour ce compteur
                    if (isset($config['agreg'][$key])) {
                        fn_gen_rq("data_appel_emis_valeur_" . $config['agreg'][$key], $MSISDN, $tbUpd);
                    }
                }
            }
        }

        if (!isset($allRqAttr['emis'][$MSISDN]['heure']) || $allRqAttr['emis'][$MSISDN]['heure'] < $tmCdr) {
            $allRqAttr['emis'][$MSISDN]['heure'] = $tmCdr;
            $allRqAttr['emis'][$MSISDN]['req_val'] = (int) $tb['balance']
                    . '||' . $tb['profil']
                    . '||' . substr($tb['status'], 0, 1)
                    . '||' . $tb['imsi']
                    . '||' . $tb['imsi_correcte']
                    . '||' . $tb['msc']
                    . '||' . $tb['cellid']
                    . '||' . implode('::', array_keys($tb['prefix']));
        }
    }
}

function execute_attribut($tbMSISDN, $allRqAttr, $connection) {
    try {
        if (count($tbMSISDN)) {
            $chaineMSISDN = "'" . implode("','", $tbMSISDN) . "'";
            $tbNtMSISDN = $tbAttrMSISDN = $tbAttrMSISDNdate = array();

            $reqVerifNum = 'SELECT numero, dt_appel_recu_total,dt_appel_recu_onnet,dt_appel_recu_offnet,dt_appel_recu_spe,dt_appel_recu_roa,
                dt_appel_recu_int,dt_appel_emis_total, dt_appel_emis_roa, dt_appel_emis_onnet, 
                dt_appel_emis_offnet, dt_appel_emis_spe, dt_appel_emis_int, dt_localisation, dt_profil, dt_status,
		dt_balance, dt_imsi FROM data_attribut WHERE numero in (' . $chaineMSISDN . ')';

            $result = $connection->query($reqVerifNum);
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//            $tbAttrMSISDN[] = $ligne->numero;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_total'] = $ligne->dt_appel_recu_total;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_onnet'] = $ligne->dt_appel_recu_onnet;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_offnet'] = $ligne->dt_appel_recu_offnet;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_spe'] = $ligne->dt_appel_recu_spe;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_int'] = $ligne->dt_appel_recu_int;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_roa'] = $ligne->dt_appel_recu_roa;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_emis_total'] = $ligne->dt_appel_emis_total;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_emis_roa'] = $ligne->dt_appel_emis_roa;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_emis_onnet'] = $ligne->dt_appel_emis_onnet;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_emis_offnet'] = $ligne->dt_appel_emis_offnet;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_emis_spe'] = $ligne->dt_appel_emis_spe;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_emis_int'] = $ligne->dt_appel_emis_int;
                $tbAttrMSISDNdate[$ligne->numero]['dt_localisation'] = $ligne->dt_localisation;
                $tbAttrMSISDNdate[$ligne->numero]['dt_profil'] = $ligne->dt_profil;
                $tbAttrMSISDNdate[$ligne->numero]['dt_status'] = $ligne->dt_status;
                $tbAttrMSISDNdate[$ligne->numero]['dt_balance'] = $ligne->dt_balance;
                $tbAttrMSISDNdate[$ligne->numero]['dt_imsi'] = $ligne->dt_imsi;
            }

            //#####################################################################
            // ********* update de la table attr

            if (isset($allRqAttr['emis'])) {
                foreach ($allRqAttr['emis'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $pfx_arr = explode('::', $ChVals[7]);
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
                        if ($tbAttrMSISDNdate[$ms]['dt_localisation'] < $tb['heure'])
                            $cnd[] = "dt_localisation = '" . $tb['heure'] . "', msc = '" . $ChVals[5] . "', cellid = '" . $ChVals[6] . "'";
                        foreach ($pfx_arr as $pfx) {
                            if ($pfx != 'pays') {
                                if ($tbAttrMSISDNdate[$ms]['dt_appel_emis_' . $pfx] < $tb['heure'])
                                    $cnd[] = "dt_appel_emis_$pfx = '" . $tb['heure'] . "'";
                            }
                        }
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $updImsi = ($ChVals[4]) ? "'" . $tb['heure'] . "', '" . $ChVals[3] . "'" : "NULL, NULL";
                        $str_ent = $str_val = '';
                        foreach ($pfx_arr as $pfx) {
                            if ($pfx != 'pays') {
                                $str_ent .= ', dt_appel_emis_' . $pfx;
                                $str_val .= ", '" . $tb['heure'] . "'";
                            }
                        }
                        $req = "INSERT INTO data_attribut (numero, dt_balance, balance, dt_profil, profil, dt_status, status, dt_localisation, 
                        msc, cellid, dt_imsi, imsi $str_ent) 
                            VALUES
                            ('" . $ms . "', '" . $tb['heure'] . "', " . $ChVals[0] . ", '" . $tb['heure'] . "', " . $ChVals[1] . ", '" . $tb['heure'] . "', 
                                " . $ChVals[2] . ", '" . $tb['heure'] . "', '" . $ChVals[5] . "', '" . $ChVals[6] . "', $updImsi $str_val)";

                        // Ajoutons les attributs pour éviter une réinsertion si le numéro existe aussi dans les appels reçus

                        $tbAttrMSISDNdate[$ms]['dt_appel_recu_total'] = '';
                        $tbAttrMSISDNdate[$ms]['dt_localisation'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_profil'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_status'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_balance'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_imsi'] = $tb['heure'];
                    }
                    if ($req != '') {
//                    echo "\r\n <br>".$req.';';
                        $result = $connection->query($req);
                    }
                }
            }

            if (isset($allRqAttr['recu'])) {
                foreach ($allRqAttr['recu'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $pfx_arr = explode('::', $ChVals[7]);

                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {

                        if ($tbAttrMSISDNdate[$ms]['dt_balance'] < $tb['heure'])
                            $cnd[] = "dt_balance = '" . $tb['heure'] . "', balance = " . $ChVals[0];
                        if ($tbAttrMSISDNdate[$ms]['dt_profil'] < $tb['heure'])
                            $cnd[] = "dt_profil = '" . $tb['heure'] . "', profil = " . $ChVals[1];
                        if ($tbAttrMSISDNdate[$ms]['dt_status'] < $tb['heure'])
                            $cnd[] = "dt_status = '" . $tb['heure'] . "', status = " . $ChVals[2];
                        if ($tbAttrMSISDNdate[$ms]['dt_localisation'] < $tb['heure'])
                            $cnd[] = "dt_localisation = '" . $tb['heure'] . "', msc = '" . $ChVals[5] . "', cellid = '" . $ChVals[6] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_imsi'] < $tb['heure'] && $ChVals[4])
                            $cnd[] = "dt_imsi = '" . $tb['heure'] . "', imsi = '" . $ChVals[3] . "'";
                        foreach ($pfx_arr as $pfx) {
                            if ($pfx != 'pays') {
                                if ($tbAttrMSISDNdate[$ms]['dt_appel_recu_' . $pfx] < $tb['heure'])
                                    $cnd[] = "dt_appel_recu_$pfx = '" . $tb['heure'] . "'";
                            }
                        }
                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $str_ent = $str_val = '';
                        foreach ($pfx_arr as $pfx) {
                            if ($pfx != 'pays') {
                                $str_ent .= ', dt_appel_recu_' . $pfx;
                                $str_val .= ", '" . $tb['heure'] . "'";
                            }
                        }
                        $updImsi = ($ChVals[4]) ? "'" . $tb['heure'] . "', '" . $ChVals[3] . "'" : "NULL, NULL";
                        $req = "INSERT INTO data_attribut (numero, dt_balance, balance, dt_profil, profil, dt_status, status, dt_localisation, 
                        msc, cellid, dt_imsi, imsi $str_ent) 
                            VALUES
                            ('" . $ms . "', '" . $tb['heure'] . "', " . $ChVals[0] . ", '" . $tb['heure'] . "', " . $ChVals[1] . ", '" . $tb['heure'] . "', 
                                " . $ChVals[2] . ", '" . $tb['heure'] . "', '" . $ChVals[5] . "', '" . $ChVals[6] . "', $updImsi $str_val )";
                    }
                    if ($req != '') {
//                    echo "\r\n <br>" . $req . ';';
                        $result = $connection->query($req);
                    }
                }
            }
        } // if MSISDN
    } catch (Exception $e) {
        throw($e);
    }
}

?>