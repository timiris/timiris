<?php

function adjustTime($chaine = '') {
    return substr('20' . $chaine, 0, 14);
}

function traitement_areaCode($chaine = '') {
    $code = couper(inverse_pos(substr($chaine, 0, 4)), '0_1');
    $op = inverse_pos(substr($chaine, 4, 2));
    $lac = substr('0000' . hexdec(substr($chaine, 6, 4)), -5);
    $ci = substr('0000' . hexdec(substr($chaine, 10, 4)), -5);
    return $code . $op . $lac . $ci;
}

function couper($chaine = '', $sup = '') {
    $lnSup = explode('_', $sup, 2);
    $deb = $lnSup[0];
    $ln = strlen($chaine) - $deb;
    if (isset($lnSup[1]) && substr($chaine, -1, 1) == 'F')
        $ln -= $lnSup[1];
    return substr($chaine, $deb, $ln);
}

function inverse_pos($chaine = '') {
    $chaineInv = '';
    for ($i = 0; $i < strlen($chaine) - 1; $i++) {
        $chaineInv .= $chaine[$i + 1] . $chaine[$i];
        $i++;
    }
    if (strlen($chaine) % 2)
        $chaineInv .= (isset($chaine[$i + 1]) ? $chaine[$i + 1] : '') . $chaine[$i];
    return $chaineInv;
}

function hxp($pos = 0, $chaine = '') {
    global $content;
    if ($chaine == '')
        return strtoupper(substr('0' . dechex(ord($content[$pos])), -2));
    else {
        // echo strlen($chaine) . ' : ' .$pos.'<br>';
        return strtoupper(substr('0' . dechex(ord($chaine[$pos])), -2));
    }
}

function convert($deb = 0, $fin = 0) {
    $chConv = '';
    for ($i = $deb; $i <= $fin; $i++)
        $chConv .= hxp($i);
    return $chConv;
}

function parse_cdr($deb, $fin, $ta) {
    switch ($ta) {
        case 'A0' : $tableName = 'MOC';
            break;
        case 'A1' : $tableName = 'MTC';
            break;
        case 'A6' : $tableName = 'SMO';
            break;
        case 'A7' : $tableName = 'SMT';
            break;
        case 'BF' : $tableName = 'BF';
            break;
        default : return false;
    }
    //***********************************
    global $config, ${$tableName};
    //***********************************

    for ($i = $deb; $i <= $fin; $i++) {
        $codeTag = hxp($i);
        if (isset(${$tableName}[$codeTag])) {
            $i++;
            ${$tableName}['lg'][$codeTag] = hexdec(hxp($i));
            ${$tableName}['val'][$codeTag] = '';
            for ($j = 1; $j <= ${$tableName}['lg'][$codeTag]; $j++) {
                $i++;
                ${$tableName}['val'][$codeTag] .= hxp($i);
            }
        } else {
            $i++;
            $codeTag .= hxp($i);
            if (isset(${$tableName}[$codeTag])) { // Sur 2 positions
                $i++;
                ${$tableName}['lg'][$codeTag] = hexdec(hxp($i));
                ${$tableName}['val'][$codeTag] = '';
                for ($j = 1; $j <= ${$tableName}['lg'][$codeTag]; $j++) {
                    $i++;
                    ${$tableName}['val'][$codeTag] .= hxp($i);
                }
            } else {
                $i++;
                $codeTag .= hxp($i);
                if (isset(${$tableName}[$codeTag])) { // Sur 3 positions
                    $i++;
                    ${$tableName}['lg'][$codeTag] = hexdec(hxp($i));
                    ${$tableName}['val'][$codeTag] = '';
                    for ($j = 1; $j <= ${$tableName}['lg'][$codeTag]; $j++) {
                        $i++;
                        ${$tableName}['val'][$codeTag] .= hxp($i);
                    }
                } else {
                    echo $codeTag . ' Non trouvé';
                    return false;
                }
            }
        }
    }
    foreach (${$tableName}['tp'] as $codeTag => $tb_fn) {
        foreach ($tb_fn as $fn => $pr) {
            if (isset(${$tableName}['val'][$codeTag])) {
                if ($pr != '')
                    ${$tableName}['val'][$codeTag] = $fn(${$tableName}['val'][$codeTag], $pr);
                else
                    ${$tableName}['val'][$codeTag] = $fn(${$tableName}['val'][$codeTag]);
            }
        }
    }
//    echo '<hr>';
    $cdr = array();
    foreach ($config[$tableName] as $key => $tag) {
        $cdr[$key] = ${$tableName}['val'][$tag];
    }
//    print_r($cdr);
//    echo '<br><br>';
//    foreach (${$tableName} as $tag => $val) {
//        if ($tag != 'val' && $tag != 'tp' && isset(${$tableName}['val'][$tag]))
//            echo $tag . ' : ' . $val . ' : ' . ${$tableName}['val'][$tag] . ', ';
//    }
    ${$tableName}['val'] = null;
    unset(${$tableName}['val']);
    return $cdr;
}

function GenInfosCdrSMT($licdr) {
    global $config;
    if (substr($licdr['msisdn'], 0, strlen($config['code_op'])) != $config['code_op'])
        return $licdr;
    $licdr['considere'] = 1;
    $licdr['prefix']['total'] = 1;
    $licdr['prefix']['pays'] = 1;
    if (substr($licdr['cellid'], 0, strlen($config['network_code'])) == $config['network_code']) { // dans le pays
        if (substr($licdr['serviceCentre'], 0, strlen($config['code_pays'])) == $config['code_pays']) {
            //*************************************
            if (is_numeric($licdr['msisdn_autre'])) {
                if (strlen($licdr['msisdn_autre']) == $config['ln_nat'] && in_array($config['code_pays'] . substr($licdr['msisdn_autre'], 0, 1), $config['national'])) { // dans le pays
                    $licdr['msisdn_autre'] = $config['code_pays'] . $licdr['msisdn_autre'];
                }

                if (strlen($licdr['msisdn_autre']) > $config['ln_nat'] && substr($licdr['msisdn_autre'], 0, strlen($config['code_pays'])) != $config['code_pays']) {
                    $licdr['prefix']['int'] = 1;
                } else {
                    if (strlen($licdr['msisdn_autre']) == $config['ln_int'] && substr($licdr['msisdn_autre'], 0, strlen($config['code_pays'])) == $config['code_pays']) {
                        if (in_array(substr($licdr['msisdn_autre'], 0, 4), $config['offnet']))
                            $licdr['prefix']['offnet'] = 1;
                        else
                            $licdr['prefix']['onnet'] = 1;
                    }
                    else
                        $licdr['prefix']['spe'] = 1;
                }
            }
            else
                $licdr['prefix']['spe'] = 1;
            //*************************************
        }else { // Appel international
            $licdr['prefix']['int'] = 1;
        }
    } else { // Code MSC incorrect
        $fo = fopen($rep_log_ignored . "MSC_SMT_" . $fileName, 'a');
        $licdr['cause'] = 'MSC, incorrecte';
        fputs($fo, json_encode($licdr) . "\r\n");
        fclose($fo);
        return $licdr;
    }
    return $licdr;
}

function GenInfosCdrMTC($licdr) {
    global $config, $fileName;
    if (substr($licdr['msisdn'], 0, strlen($config['code_op'])) != $config['code_op'])
        return $licdr;
    if (substr($licdr['msc'], 0, strlen($config['msc'])) == $config['msc']) { // dans le pays
        $licdr['considere'] = 1;
        $licdr['prefix']['total'] = 1;
        $licdr['prefix']['pays'] = 1;
        if (substr($licdr['msisdn_autre'], 0, strlen($config['code_pays'])) == $config['code_pays']) {
            if (strlen($licdr['msisdn_autre']) < $config['ln_int']) { // ON-Net
                $licdr['prefix']['spe'] = 1;
            } elseif (substr($licdr['msisdn_autre'], 0, strlen($config['code_op'])) == $config['code_op']) {
                $licdr['prefix']['onnet'] = 1;
            } else {
                $licdr['prefix']['offnet'] = 1;
            }
        } else { // Appel international
            $licdr['prefix']['int'] = 1;
        }
    } else { // Code MSC incorrect
        $fo = fopen($rep_log_ignored . "MSC_MTC_" . $fileName, 'a');
        $licdr['cause'] = 'MSC, incorrecte';
        fputs($fo, json_encode($licdr) . "\r\n");
        fclose($fo);
        return $licdr;
    }
    return $licdr;
}

function GenRq($tb, $tbc, $tag) {
    // Generation Req Attribut
    global $allRq, $tbMSISDN, $allRqAttr;
    $tabChN = $tabChD = array();
    $MSISDN = $tb['msisdn'];
    $tmCdr = $tb['heure'];
    if (!in_array($MSISDN, $tbMSISDN))
        $tbMSISDN[] = $MSISDN;
    $dj = substr($tmCdr, 0, 8);
    $dm = substr($tmCdr, 0, 6);
    $da = substr($tmCdr, 0, 4);
    if (isset($tbc[$dj])) {
        $nmCh = $tbc[$dj];
        $tabChN[$nmCh] = 1;
        if ($tag == 'A1')
            $tabChD[$nmCh] = $tb['duree'];
    }
    if (isset($tbc[$dm])) {
        $nmCh = $tbc[$dm];
        $tabChN[$nmCh] = 1;
        if ($tag == 'A1')
            $tabChD[$nmCh] = $tb['duree'];
    }
    if (isset($tbc[$da])) {
        $nmCh = $tbc[$da];
        $tabChN[$nmCh] = 1;
        if ($tag == 'A1')
            $tabChD[$nmCh] = $tb['duree'];
    }
    $tb['compteur'] = array();
    $tb['dureeFacturee'] = 0;
    foreach ($tb['prefix'] as $prfx => $val) {
        if ($tag == 'A1') { // Appel reçu
            fn_gen_rq("data_appel_recu_nombre_$prfx", $MSISDN, $tabChN);
            fn_gen_rq("data_appel_recu_duree_$prfx", $MSISDN, $tabChD);

            if (!isset($allRqAttr['appel'][$MSISDN]['heure']) || $allRqAttr['appel'][$MSISDN]['heure'] < $tmCdr) {
                $allRqAttr['appel'][$MSISDN]['heure'] = $tmCdr;
                $allRqAttr['appel'][$MSISDN]['req_val'] = $tb['imsi']
                        . '||' . $tb['msc']
                        . '||' . $tb['cellid']
                        . '||' . implode('::', array_keys($tb['prefix']));
            }
        } else {    // SMS Reçu
            fn_gen_rq("data_sms_recu_nombre_$prfx", $MSISDN, $tabChN);

            if (!isset($allRqAttr['sms'][$MSISDN]['heure']) || $allRqAttr['sms'][$MSISDN]['heure'] < $tmCdr) {
                $allRqAttr['sms'][$MSISDN]['heure'] = $tmCdr;
                $allRqAttr['sms'][$MSISDN]['req_val'] = $tb['imsi']
                        . '||' . $tb['msc']
                        . '||' . $tb['cellid']
                        . '||' . implode('::', array_keys($tb['prefix']));
            }
        }
    }

    return true;
}

function execute_attribut($tbMSISDN, $allRqAttr, $connection) {
    try {
        if (count($tbMSISDN)) {
            $chaineMSISDN = "'" . implode("','", $tbMSISDN) . "'";
            $tbNtMSISDN = $tbAttrMSISDN = $tbAttrMSISDNdate = array();

            $reqVerifNum = 'SELECT numero, dt_localisation, dt_appel_recu_total, dt_sms_recu, dt_imsi, dt_appel_recu_onnet, dt_appel_recu_offnet,
                dt_appel_recu_spe, dt_appel_recu_int, dt_appel_recu_roa
			FROM data_attribut WHERE numero in (' . $chaineMSISDN . ')';

            $result = $connection->query($reqVerifNum);
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//            $tbAttrMSISDN[] = $ligne->numero;
                $tbAttrMSISDNdate[$ligne->numero]['dt_localisation'] = $ligne->dt_localisation;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_total'] = $ligne->dt_appel_recu_total;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_onnet'] = $ligne->dt_appel_recu_onnet;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_offnet'] = $ligne->dt_appel_recu_offnet;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_spe'] = $ligne->dt_appel_recu_spe;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_int'] = $ligne->dt_appel_recu_int;
                $tbAttrMSISDNdate[$ligne->numero]['dt_appel_recu_roa'] = $ligne->dt_appel_recu_roa;
                $tbAttrMSISDNdate[$ligne->numero]['dt_sms_recu'] = $ligne->dt_sms_recu;
                $tbAttrMSISDNdate[$ligne->numero]['dt_imsi'] = $ligne->dt_imsi;
            }

            //#####################################################################
            // ********* update de la table attr

            if (isset($allRqAttr['appel'])) {
                foreach ($allRqAttr['appel'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $pfx_arr = explode('::', $ChVals[3]);
                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {
                        if ($tbAttrMSISDNdate[$ms]['dt_imsi'] < $tb['heure'])
                            $cnd[] = "dt_imsi = '" . $tb['heure'] . "', imsi = '" . $ChVals[0] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_localisation'] < $tb['heure'])
                            $cnd[] = "dt_localisation = '" . $tb['heure'] . "', msc = '" . $ChVals[1] . "', cellid = '" . $ChVals[2] . "'";
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
                        $req = "INSERT INTO data_attribut (numero, dt_imsi, imsi, dt_localisation, msc, cellid $str_ent)  VALUES
                        ('" . $ms . "', '" . $tb['heure'] . "', '" . $ChVals[0] . "', '" . $tb['heure'] . "', '" . $ChVals[1] . "', '" . $ChVals[2] . "' $str_val)";
                        $tbAttrMSISDNdate[$ms]['dt_imsi'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_appel_recu_total'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_localisation'] = $tb['heure'];
                        $tbAttrMSISDNdate[$ms]['dt_sms_recu'] = '';
                    }
                    if ($req != '') {
//                    echo "\r\n <br>" . $req . ';';
                        $result = $connection->query($req);
                    }
                }
            }

            if (isset($allRqAttr['sms'])) {
                foreach ($allRqAttr['sms'] as $ms => $tb) {
                    $ChVals = explode('||', $tb['req_val']);
                    $cnd = array();
                    $req = '';
                    if (isset($tbAttrMSISDNdate[$ms])) {
                        if ($tbAttrMSISDNdate[$ms]['dt_imsi'] < $tb['heure'])
                            $cnd[] = "dt_imsi = '" . $tb['heure'] . "', imsi = '" . $ChVals[0] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_sms_recu'] < $tb['heure'])
                            $cnd[] = "dt_sms_recu = '" . $tb['heure'] . "'";
                        if ($tbAttrMSISDNdate[$ms]['dt_localisation'] < $tb['heure'])
                            $cnd[] = "dt_localisation = '" . $tb['heure'] . "', msc = '" . $ChVals[1] . "', cellid = '" . $ChVals[2] . "'";

                        if (count($cnd))
                            $req = 'UPDATE data_attribut SET ' . implode(', ', $cnd) . " WHERE numero = '" . $ms . "'";
                    }else {
                        $req = "INSERT INTO data_attribut (numero, dt_imsi, imsi, dt_localisation, msc, cellid, dt_sms_recu)  VALUES
                        ('" . $ms . "', '" . $tb['heure'] . "', '" . $ChVals[0] . "', '" . $tb['heure'] . "', '" . $ChVals[1] . "', '" . $ChVals[2] . "', '" . $tb['heure'] . "')";
                    }
                    if ($req != '') {
//                    echo "\r\n <br>" . $req . ';';
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