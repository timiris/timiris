<?php

function execute_attribut($allRqAttr, $connection) {
    $new = $upd = 0;
    $dateJ = strtotime(date("YmdHis"));                        //2016-11-30
    $dtJM1 = date("YmdHis", strtotime("-1 days", $dateJ));
    try {
        if (count($allRqAttr)) {
            $chaineMSISDN = "'" . implode("','", array_keys($allRqAttr)) . "'";
            $tbNtMSISDN = $tbAttrMSISDN = $tbAttrMSISDNdate = array();

            $reqVerifNum = 'SELECT numero, is_lang_ar, profil,balance,dt_active,dt_active_stop,dt_suspend_stop, dt_balance, dt_profil, dt_status,
                dt_disable_stop,status, imsi FROM data_attribut WHERE numero in (' . $chaineMSISDN . ')';

            $result = $connection->query($reqVerifNum);
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//            $tbAttrMSISDN[] = $ligne->numero;
                if(!isset($tbAttrMSISDNdate[$ligne->numero])) $tbAttrMSISDNdate[$ligne->numero]=array();
                $tbAttrMSISDNdate[$ligne->numero]['profil'] = $ligne->profil;
                $tbAttrMSISDNdate[$ligne->numero]['dt_profil'] = $ligne->dt_profil;
                $tbAttrMSISDNdate[$ligne->numero]['balance'] = $ligne->balance;
                $tbAttrMSISDNdate[$ligne->numero]['dt_balance'] = $ligne->dt_balance;
                $tbAttrMSISDNdate[$ligne->numero]['status'] = $ligne->status;
                $tbAttrMSISDNdate[$ligne->numero]['dt_status'] = $ligne->dt_status;
                $tbAttrMSISDNdate[$ligne->numero]['dt_active'] = $ligne->dt_active;
                $tbAttrMSISDNdate[$ligne->numero]['dt_active_stop'] = $ligne->dt_active_stop;
                $tbAttrMSISDNdate[$ligne->numero]['dt_suspend_stop'] = $ligne->dt_suspend_stop;
                $tbAttrMSISDNdate[$ligne->numero]['dt_disable_stop'] = $ligne->dt_disable_stop;
                $tbAttrMSISDNdate[$ligne->numero]['imsi'] = $ligne->imsi;
                $tbAttrMSISDNdate[$ligne->numero]['is_lang_ar'] = ($ligne->is_lang_ar) ? 'true' : 'false';
            }

            //#####################################################################
            // ********* update de la table attr

            foreach ($allRqAttr as $ms => $tb) {
                $cnd = array();
                $req = '';
                if (isset($tbAttrMSISDNdate[$ms])) {
                    if ($tb['status'] == 0 && $tbAttrMSISDNdate[$ms]['dt_active'] > $dtJM1)
                        continue;
                    if ($tbAttrMSISDNdate[$ms]['profil'] != $tb['profil'] and $tbAttrMSISDNdate[$ms]['dt_profil'] < $tb['heure'])
                        $cnd[] = "dt_profil = '" . $tb['heure'] . "', profil = " . $tb['profil'];
                    if ($tbAttrMSISDNdate[$ms]['balance'] != $tb['balance'] and $tbAttrMSISDNdate[$ms]['dt_balance'] < $tb['heure'])
                        $cnd[] = "dt_balance = '" . $tb['heure'] . "', balance = " . $tb['balance'];
                    if ($tbAttrMSISDNdate[$ms]['status'] != $tb['status'] and $tbAttrMSISDNdate[$ms]['dt_status'] < $tb['heure'])
                        $cnd[] = "dt_status = '" . $tb['heure'] . "', status = " . $tb['status'];
                    if ($tbAttrMSISDNdate[$ms]['dt_active'] != $tb['dt_active'])
                        $cnd[] = "dt_active = '" . $tb['dt_active'] . "'";
                    if ($tbAttrMSISDNdate[$ms]['dt_active_stop'] != $tb['dt_active_stop'])
                        $cnd[] = "dt_active_stop = '" . $tb['dt_active_stop'] . "'";
                    if ($tbAttrMSISDNdate[$ms]['dt_suspend_stop'] != $tb['dt_suspend_stop'])
                        $cnd[] = "dt_suspend_stop = '" . $tb['dt_suspend_stop'] . "'";
                    if ($tbAttrMSISDNdate[$ms]['dt_disable_stop'] != $tb['dt_disable_stop'])
                        $cnd[] = "dt_disable_stop = '" . $tb['dt_disable_stop'] . "'";
                    if ($tbAttrMSISDNdate[$ms]['is_lang_ar'] != $tb['is_lang_ar'])
                        $cnd[] = "is_lang_ar = '" . $tb['is_lang_ar'] . "'";
                    if (($tbAttrMSISDNdate[$ms]['imsi'] == '' || ($tb['status'] == 0 and $tb['imsi'] != $tbAttrMSISDNdate[$ms]['imsi'])) and strlen($tb['imsi']) == 15 and substr($tb['imsi'], 0, 5) == '60901')
                        $cnd[] = "imsi = '" . $tb['imsi'] . "'";

                    if (count($cnd)) {
                        $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                        $upd++;
                    }
                } else {
                    if ($tb['status'] == 0) {
                        $tb['dt_active_stop'] = $tb['dt_suspend_stop'] = $tb['dt_disable_stop'] = '';
                    }
                    $req = "INSERT INTO data_attribut (numero, is_lang_ar, dt_balance, balance, dt_profil, profil, dt_status, status, dt_active, dt_active_stop, dt_suspend_stop, dt_disable_stop, imsi) 
                            VALUES
                            ('" . $ms . "','" . $tb['is_lang_ar'] . "','" . $tb['heure'] . "'," . $tb['balance'] . ",'" . $tb['heure'] . "'," . $tb['profil'] . ",'" . $tb['heure'] . "'," . $tb['status'] . ", 
                                '" . $tb['dt_active'] . "', '" . $tb['dt_active_stop'] . "', '" . $tb['dt_suspend_stop'] . "', '" . $tb['dt_disable_stop'] . "' , '" . $tb['imsi'] . "' )";
                    $new++;
                }
                if ($req != '') {
//                    echo "\r\n <br>" . $req . ';';
                    $result = $connection->query($req);
                }
            }
        } // if MSISDN
    } catch (Exception $e) {
        throw($e);
    }
    return array('new' => $new, 'upd' => $upd);
}

?>