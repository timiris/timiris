<?php

//$repGlobal = 'E:/depot/test';
$repGlobal = '';
$rep_chargement = $repGlobal . '/tim_DATA/cdrs/chargement/in/';
$rep_doublons = $repGlobal . '/tim_DATA/cdrs/doublons/in/';
$rep_file_rejected = $repGlobal . '/tim_DATA/cdrs/rejected/in/';
$rep_file_error = $repGlobal . '/tim_DATA/cdrs/error/in/';
$rep_sauv = $repGlobal . '/tim_arch/sauvegarde/in/';
$rep_log = $repGlobal . '/tim_log/log_NewEvent/';
$rep_log_ignored = $repGlobal . '/tim_log/log_ignored/in/';


if (!isset($rep))
    $rep = '../';
require_once 'connection.php';
require_once $rep . "defs.php";
require_once "mail/envoyer_mail.php";

$config = $NewPos = $NewVal = $grp_dec = $bns_dec = $event_corsp = $cmp_lim = $arr_valorisation = $arrCible = $arrGT = array();
$monServices = array('1257446764', '1457450091', '1857361948', '1857447070' , '148211097');

$NewPos['compteur1'] = 105;
$NewPos['cout1'] = 107;
$NewPos['oper_type1'] = 109;
$NewPos['compteur2'] = 117;
$NewPos['cout2'] = 119;
$NewPos['oper_type2'] = 121;
$NewPos['compteur3'] = 129;
$NewPos['cout3'] = 131;
$NewPos['oper_type3'] = 133;
$NewPos['compteur4'] = 141;
$NewPos['cout4'] = 143;
$NewPos['oper_type4'] = 145;
$NewPos['compteur5'] = 153;
$NewPos['cout5'] = 155;
$NewPos['oper_type5'] = 157;
$NewPos['compteur6'] = 165;
$NewPos['cout6'] = 167;
$NewPos['oper_type6'] = 169;
$NewPos['compteur7'] = 177;
$NewPos['cout7'] = 179;
$NewPos['oper_type7'] = 181;
$NewPos['compteur8'] = 189;
$NewPos['cout8'] = 191;
$NewPos['oper_type8'] = 193;
$NewPos['compteur9'] = 201;
$NewPos['cout9'] = 203;
$NewPos['oper_type9'] = 205;
$NewPos['compteur10'] = 213;
$NewPos['cout10'] = 215;
$NewPos['oper_type10'] = 217;


$config['separateur'] = '|';
$config['flux_ocs'] = 'new';
$config['flux_msc'] = 'old';
$config['compteur_principal'] = '2000';
$config['code_pays'] = '222';
$config['ln_int'] = 11;
$config['ln_nat'] = 8;
$config['ln_imsi'] = 15;
$config['nb_files'] = 6;
$config['nbrFichierCharge'] = 60;

//***********************
//***********************
//***********************
$config['network_code'] = '60901';
$config['code_op'] = '2223';
$config['offnet'] = array(2222, 2224);
$config['national'] = array(2222, 2223, 2224);
$config['nat'] = array(2, 3, 4);

//***********************
//***********************
//***********************
$decNatureCdrName = array();
$decNatureCdrName['vou'] = '5, 12';
$decNatureCdrName['rec'] = '2, 3, 12';
$decNatureCdrName['sms'] = '4, 9, 12';
$decNatureCdrName['mgr'] = '6, 8, 12';
$decNatureCdrName['data'] = '7, 12';
$decNatureCdrName['b'] = '3';
$decNatureCdrName['mon'] = '8, 12';
$decNatureCdrName['clr'] = '99999';

//***********************
//***********************
//***********************

function verifierNumero($numero, $config) {
    $ln_cd_pays = strlen($config['code_pays']);
    if (substr($numero, 0, 2) == '00')
        $numero = substr($numero, 2, strlen($numero) - 2);
    if (strlen($numero) == $config['ln_nat'] && in_array(substr($numero, 0, 1), $config['nat']))
        $numero = $config['code_pays'] . $numero;
    if (strlen($numero) == $config['ln_int'] + $ln_cd_pays)
        $numero = substr($numero, $ln_cd_pays, strlen($numero) - $ln_cd_pays);
    return $numero;
}

function add_arrays($a, $b) {
    foreach ($b as $k => $v) {
        if (isset($a[$k]))
            $a[$k] += $v;
        else
            $a[$k] = $v;
    }
    return $a;
}

function fn_gen_rq($tb, $msisdn, $arr) {
    global $allRq;
    if (isset($allRq[$tb][$msisdn]))
        $allRq[$tb][$msisdn] = add_arrays($allRq[$tb][$msisdn], $arr);
    else
        $allRq[$tb][$msisdn] = $arr;
}

function execute_req_table($tb, $arRq, $connection) {
    try {
        global $arrCible, $arrGT;
        $tbAttrMSISDN = $arrCibTbl = $arrGtTbl = $arrCibTblVal = $arrGtTblVal = array();
        $nums = array_keys($arRq);

        foreach ($arrCible as $idCmp => $arrNums) {
            $arrCibTbl[$idCmp] = array_intersect($arrNums, $nums);
            if (!count($arrCibTbl[$idCmp]))
                unset($arrCibTbl[$idCmp]);
            else
                $arrCibTblVal[$idCmp] = 0;
        }
        foreach ($arrGT as $idCmp => $arrNums) {
            $arrGtTbl[$idCmp] = array_intersect($arrNums, $nums);
            if (!count($arrGtTbl[$idCmp]))
                unset($arrGtTbl[$idCmp]);
            else
                $arrGtTblVal[$idCmp] = 0;
        }

        foreach ($arRq as $num => $arChVal) {
            $arrCh = array_keys($arChVal);
            foreach ($arrCibTbl as $idC => $arr_nc) {
                if (in_array($num, $arr_nc)) {
                    foreach ($arrCh as $col)
                        if (substr($col, 0, 1) == 'a')
                            $arrCibTblVal[$idC]+= $arRq[$num][$col];
                }
            }
            foreach ($arrGtTbl as $idC => $arr_nc) {
                if (in_array($num, $arr_nc)) {
                    foreach ($arrCh as $col)
                        if (substr($col, 0, 1) == 'a')
                            $arrGtTblVal[$idC]+= $arRq[$num][$col];
                }
            }
        }

        foreach ($arrCibTblVal as $idCmp => $val)
            $connection->query('UPDATE app_campagne_kpi SET cible = cible + ' . $val . " WHERE tbname = '$tb' AND fk_id_campagne = $idCmp");
        foreach ($arrGtTblVal as $idCmp => $val)
            $connection->query('UPDATE app_campagne_kpi SET gc = gc + ' . $val . " WHERE tbname = '$tb' AND fk_id_campagne = $idCmp");


        $chaineMSISDN = "'" . implode("','", $nums) . "'";
        $reqVerifNum = 'SELECT numero FROM ' . $tb . ' WHERE numero in (' . $chaineMSISDN . ')';
        $result = $connection->query($reqVerifNum);
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            $tbAttrMSISDN[] = $ligne->numero;
        }



        $tbNtMSISDN = array_diff($nums, $tbAttrMSISDN);
        if (count($tbNtMSISDN)) {
            foreach ($tbNtMSISDN as $num) {
                $tReq = 'INSERT INTO ' . $tb . ' (numero, ' . implode(', ', array_keys($arRq[$num])) . ')
                            VALUES (\'' . $num . '\', ' . implode(', ', array_values($arRq[$num])) . ')';
                $result = $connection->query($tReq);
            }
        }

// ********* update des tables data
        foreach ($tbAttrMSISDN as $num) {
            $ar = array();
            foreach ($arRq[$num] as $ch => $v)
                $ar[] = "$ch = $ch + $v";
            $reqUpdate = "UPDATE $tb SET " . implode(', ', $ar) . " WHERE numero = '$num'";
//                echo "\r\n <br>" . $reqUpdate . ';';
            $result = $connection->query($reqUpdate);
        }
    } catch (Exception $e) {
        throw($e);
    }
}

function execute_requete($tbMSISDN, $allRq, $connection) {
    try {
        if (count($tbMSISDN)) {
            $chaineMSISDN = "'" . implode("','", $tbMSISDN) . "'";
            global $arrCible, $arrGT, $allRqAttr;
            $arrCible = array();
            $reqCmp = 'select ac.id, numero from app_campagne ac
                join app_campagne_cible acc on acc.fk_id_campagne = ac.id
                where (ac.etat = ' . CMP_ENCOURS . ' or ac.etat = ' . CMP_SUSPENDUE . ') and numero in (' . $chaineMSISDN . ')';
            $res = $connection->query($reqCmp);
            while ($li = $res->fetch(PDO::FETCH_OBJ)) {
                if (!isset($arrCible[$li->id]))
                    $arrCible[$li->id] = array();
                $arrCible[$li->id][] = $li->numero;
            }
            if (isset($allRqAttr['activation']) && count($allRqAttr['activation'])) {
                $NewActivation = array_keys($allRqAttr['activation']);
                $rqCiblage = $connection->query('select id from app_campagne where (etat = ' . CMP_ENCOURS . ' or etat = ' . CMP_SUSPENDUE . ') and id_cible = 0');
                while ($CmpGl = $rqCiblage->fetch(5)) {
                    $idCG = $CmpGl->id;
                    if (isset($arrCible[$idCG])) {
                        $new = array_diff($NewActivation, $arrCible[$idCG]);
                        $arrCible[$idCG] = array_merge($arrCible[$idCG], $new);
                    } else {
                        $new = $arrCible[$idCG] = $NewActivation;
                    }
                    $newEx = $connection->query("select numero from app_campagne_exclus where fk_id_campagne = $idCG and numero in ('" . implode("','", $tbMSISDN) . "')")->fetchAll(PDO::FETCH_COLUMN);
                    $new = array_diff($new, $newEx);
                    if (count($new)) {
                        $listNew = '(' . $idCG . ", '" . implode("'), ($idCG, '", $new) . "')";
                        $connection->query('insert into app_campagne_cible (fk_id_campagne, numero) values ' . $listNew);
                        $connection->query('update app_campagne set nbr_cible = nbr_cible +' . count($new) . ' where id = ' . $idCG);
                    }
                }
            }

            $reqCmp = 'select ac.id, numero from app_campagne ac
                join app_campagne_exclus ace on ace.fk_id_campagne = ac.id
                where (ac.etat = ' . CMP_ENCOURS . ' or ac.etat = ' . CMP_SUSPENDUE . ') and numero in (' . $chaineMSISDN . ')';
            $res = $connection->query($reqCmp);
            while ($li = $res->fetch(PDO::FETCH_OBJ)) {
                if (!isset($arrGT[$li->id]))
                    $arrGT[$li->id] = array();
                $arrGT[$li->id][] = $li->numero;
            }

// ********* update des tables data
//#####################################################################
            foreach ($allRq as $tb => $arRq) {
                if ($tb == 'data_consommation_total')
                    continue;
                execute_req_table($tb, $arRq, $connection);
            }
            if (isset($allRq['data_consommation_total']))
                execute_req_table('data_consommation_total', $allRq['data_consommation_total'], $connection);
//#####################################################################
        } // if MSISDN
    } catch (Exception $e) {
        throw($e);
    }
}

function fn_eligibility(&$tbCDR, $cdrType) {
    try {
        $arr_gv = array();
        $debug = false;
        $arr_tc = array('rec' => array(2 => 1, 3 => 2));
        $arr_tag = array('A1' => 3, 'A7' => 9);
        global $grp_dec, $arr_localisation, $arr_dt_cmp;
        if (isset($tbCDR['cellid'])) {
            $tbCDR['localisation'] = (isset($arr_localisation[$tbCDR['cellid']])) ? $arr_localisation[$tbCDR['cellid']] : 0;
        }
        if ($tbCDR['msisdn'] == '22233222255') {
            $debug = true;
            print_r($tbCDR);
        }

        foreach ($grp_dec as $cmp => $grps) {
            if ($tbCDR['heure'] < $arr_dt_cmp[$cmp]['dtStart'] || $tbCDR['heure'] > $arr_dt_cmp[$cmp]['dtStop']) {
//            echo '<br>non éligibe cond time';
                continue;
            }
            if ($debug)
                echo "pos 44545 \n\r";
            foreach ($grps as $idGrp => $grp) {
                $vg = true;
                foreach ($grp as $id => $cr) {
                    if (isset($tbCDR['type_appel']) && isset($arr_tc[$cdrType][$cr['nature_dec']]) && $arr_tc[$cdrType][$cr['nature_dec']] != $tbCDR['type_appel']) {
                        $vg = false;
                        continue;
                    }
                    if ($debug)
                        echo "pos 86885 \n\r";
                    if (isset($tbCDR['type_tr']) && $tbCDR['type_tr'] != $cr['fk_id_td_event']) {
                        if ($cr['fk_id_td_event'] != 26) {
                            if ($debug)
                                echo 'Quitter', $tbCDR['msisdn'], ':', $tbCDR['type_tr'], ':', $tbCDR['type_tr'], ':', $cr['fk_id_td_event'], "\n\r";
                            $vg = false;
                            continue;
                        }
                    }
                    if ($debug)
                        echo 'Pass ', $tbCDR['msisdn'], ':', $tbCDR['type_tr'], ':', $tbCDR['type_tr'], ':', $cr['fk_id_td_event'], "\n\r";
                    if (isset($arr_tag[$cdrType]) && $cr['nature_dec'] != $arr_tag[$cdrType]) {
                        $vg = false;
                        continue;
                    }
                    if ($debug)
                        echo "pos 93146 \n\r";

                    $cr['operateur'] = trim($cr['operateur']);
                    if ($cr['cdrField'] == 'compteur')
                        $fieldValue = isset($tbCDR['compteur'][$cr['code']]) ? $tbCDR['compteur'][$cr['code']] : 0;
                    elseif ($cr['cdrField'] == 'prefix') {          // Traitement des champs avec valeur array
                        $fieldValue = array_keys($tbCDR['prefix']);
                        if ($cr['operateur'] == '=')
                            $cr['operateur'] = 'in';
                        elseif ($cr['operateur'] == '!=')
                            $cr['operateur'] = 'not in';
                    }
                    else
                        $fieldValue = $tbCDR[$cr['cdrField']];
                    if ($debug) {
                        echo $fieldValue, ' : ', $cr['operateur'], ':', $cr['valeur'], "\n\r";
                    }
                    switch ($cr['operateur']) {
                        case '=': if (!($fieldValue == $cr['valeur']))
                                $vg = false;
                            break;
                        case '!=':if (!($fieldValue != $cr['valeur']))
                                $vg = false;
                            break;
                        case '>=':if (!($fieldValue >= $cr['valeur']))
                                $vg = false;
                            break;
                        case '<=':if (!($fieldValue <= $cr['valeur']))
                                $vg = false;
                            break;
                        case '>':if (!($fieldValue > $cr['valeur']))
                                $vg = false;
                            break;
                        case '<':if (!($fieldValue < $cr['valeur']))
                                $vg = false;
                            break;
                        case 'like1' : if (!(substr($fieldValue, 0, strlen($cr['valeur'])) == $cr['valeur'] ))
                                $vg = false;
                            break;
                        case 'like2' : if (!(substr($fieldValue, -strlen($cr['valeur'])) == $cr['valeur']))
                                $vg = false;
                            break;
                        case 'like3' : if (strpos($fieldValue, $cr['valeur']) === false)
                                $vg = false;
                            break;
                        case 'in' : $arr_val = explode('|', $cr['valeur']);
                            if (is_array($fieldValue)) {
                                if (!count(array_intersect($fieldValue, $arr_val)))
                                    $vg = false;
                            }else {
                                if (!in_array($fieldValue, $arr_val))
                                    $vg = false;
                            }
                            break;
                        case 'not in' : $arr_val = explode('|', $cr['valeur']);
                            if (is_array($fieldValue)) {
                                if (count(array_intersect($fieldValue, $arr_val)))
                                    $vg = false;
                            }else {
                                if (in_array($fieldValue, $arr_val))
                                    $vg = false;
                            }
                            break;
                    }
                }
                if ($vg) {   // groupe de critère vérifié => calcule de bonus
                    if (!isset($arr_gv[$cmp]))
                        $arr_gv[$cmp] = array();
                    $arr_gv[$cmp][$idGrp] = 1;
                }
            }
        }
        return $arr_gv;
    } catch (Exception $e) {
        throw ($e);
    }
}

function fn_calcul_bonus($tbCDR, $arr_gv) {
    try {
        global $arr_glb_bonus, $bns_dec, $idx_bns, $arr_valorisation;
        $convertUnitBonus = array('consommation' => 100, 'cout' => 100, 'valeur' => 100, 'montant' => 100, 'duree' => 60, 'volume' => 1048576);
        foreach ($arr_gv as $cmp => $grps) {
            $idx_bns++;
            if (!isset($arr_glb_bonus[$cmp])) {
                $arr_glb_bonus[$cmp] = $arr_glb_bonus[$cmp][$tbCDR['msisdn']] = array();
            } elseif (!isset($arr_glb_bonus[$cmp][$tbCDR['msisdn']]))
                $arr_glb_bonus[$cmp][$tbCDR['msisdn']] = array();
            foreach ($grps as $idGrp => $grp) {
                if (isset($bns_dec[$cmp][$idGrp])) {
                    $arr_glb_bonus[$cmp][$tbCDR['msisdn']][$idGrp . '|' . $idx_bns] = array();
                    foreach ($bns_dec[$cmp][$idGrp] as $idBns => $bns) {
                        if ($bns['ch_ref'] == '') {   //bonus libre
                            $valeur = $bns['valeur'] * $bns['unite'];
                            $valorisation = $bns['valeur'] * $arr_valorisation[$bns['code']];
                            $arr_glb_bonus[$cmp][$tbCDR['msisdn']][$idGrp . '|' . $idx_bns][] = array('idBns' => $idBns, 'nature' => $bns['nature'], 'code' => $bns['code'],
                                'valeur' => $valeur, 'dt_action' => $tbCDR['heure'], 'valorisation' => $valorisation);
                        } else {  // bonus proportionnel
                            if (isset($tbCDR[$bns['ch_ref']]) && $tbCDR[$bns['ch_ref']]) {
                                $valeur = (int) ($bns['valeur'] * $tbCDR[$bns['ch_ref']] / 100);
                                $valeur -= $valeur % $convertUnitBonus[$bns['ch_ref']];
                                if ($valeur > 0) {
                                    $valorisation = ($valeur / $convertUnitBonus[$bns['ch_ref']]) * $arr_valorisation[$bns['code']];
                                    if ($bns['nature'] == 1000) // cas de point de fidlité
                                        $valeur = (int) ($valeur / 100);
                                    $arr_glb_bonus[$cmp][$tbCDR['msisdn']][$idGrp . '|' . $idx_bns][] = array('idBns' => $idBns, 'nature' => $bns['nature'],
                                        'code' => $bns['code'], 'valeur' => $valeur, 'dt_action' => $tbCDR['heure'], 'valorisation' => $valorisation);
                                }
//                            echo '<br> '.$tbCDR['msisdn'].' : valeur : '.$valeur.' : ch_ref '.$bns['ch_ref'].' = '.$tbCDR[$bns['ch_ref']].', conv : '.$convertUnitBonus[$bns['ch_ref']];
                            }
                        }
                    }
                }
            }
            if (!count($arr_glb_bonus[$cmp][$tbCDR['msisdn']]))
                unset($arr_glb_bonus[$cmp][$tbCDR['msisdn']]);
            //********* Bonus sans global
            $idGrp = 0;
            if (isset($bns_dec[$cmp][$idGrp])) {
                $arr_glb_bonus[$cmp][$tbCDR['msisdn']][$idGrp . '|' . $idx_bns] = array();
                foreach ($bns_dec[$cmp][$idGrp] as $idBns => $bns) {
                    $valeur = $bns['valeur'] * $bns['unite'];
                    $valorisation = $bns['valeur'] * $arr_valorisation[$bns['code']];
                    $arr_glb_bonus[$cmp][$tbCDR['msisdn']][$idGrp . '|' . $idx_bns][] = array('idBns' => $idBns, 'nature' => $bns['nature'],
                        'code' => $bns['code'], 'valeur' => $valeur, 'dt_action' => $tbCDR['heure'], 'valorisation' => $valorisation);
                }
            }
        }
    } catch (Exception $e) {
        throw ($e);
    }
}

function execute_all_bonus($arr_glb_bonus, $connection, $dtjAction) {
    global $cmp_lim, $grp_dec;
    try {
        $limitation = array('cmp' => array(
                'cmp_nbr_bonus' => 100000000000000000000,
                'cmp_montant_bonus' => 100000000000000000000,
                'cmp_nbr_bonus_jr' => 100000000000000000000,
                'cmp_montant_bonus_jr' => 100000000000000000000),
            'client' => array(
                'client_nbr_bonus' => 100000000000000000000,
                'client_montant_bonus' => 100000000000000000000,
                'client_nbr_bonus_jr' => 100000000000000000000,
                'client_montant_bonus_jr' => 100000000000000000000)
        );
        foreach ($arr_glb_bonus as $cmp => $arr_bonus) {
            $cmpStatus = true;
            $limitation_cmp = $limitation['cmp'];
            if ($cmp_lim[$cmp]['cmp_total']) {
                $req_limitaion = "select count(distinct ab.id) cmp_nbr_bonus, sum(case when ab.valorisation is null then 0 else ab.valorisation end) cmp_montant_bonus, 
                    count(distinct (CASE WHEN substring(ab.dt_action, 1,8)='$dtjAction' then ab.id else null end)) cmp_nbr_bonus_jr,
                    sum(CASE WHEN substring(ab.dt_action, 1,8)='$dtjAction' then ab.valorisation else 0 end) cmp_montant_bonus_jr 
                    from app_campagne ac
                    left join(
                        select abn.*, abd.valorisation, abd.dt_action from app_bonus abn 
                        join app_bonus_details abd on abn.id = abd.id_bonus
                    ) ab on ab.fk_id_campagne = ac.id
                    where ac.id = $cmp
                        group by ac.id";
                $res_limitaion = $connection->query($req_limitaion);
                $ligne_lim = $res_limitaion->fetch(PDO::FETCH_OBJ);
                $bns_used = true;
                foreach ($limitation_cmp as $key => $val) {
                    if ((int) $cmp_lim[$cmp][$key] > 0) {
                        $limitation_cmp[$key] = $cmp_lim[$cmp][$key] - $ligne_lim->$key;
                        if ($limitation_cmp[$key] <= 0)
                            $bns_used = false;
                    }
                }
                if (!$bns_used)
                    continue;
                echo "\n\r Limitations campagne : ";
                //print_r($limitation_cmp);
            }

            $arr_req_bns_detail = array();
            $nums = array_keys($arr_bonus);
            $chaineMSISDN = "'" . implode("','", $nums) . "'";
            if ($cmp_lim[$cmp]['client_total']) {
                $reqVerifNum = "SELECT att.numero, has_fidelity, count(distinct ab.id) client_nbr_bonus, sum(case when ab.valorisation is null then 0 else ab.valorisation end) client_montant_bonus, 
                    count(distinct (CASE WHEN substring(ab.dt_action, 1,8)='$dtjAction' then ab.id else null end)) client_nbr_bonus_jr,
                    sum(CASE WHEN substring(ab.dt_action, 1,8)='$dtjAction' then ab.valorisation else 0 end) client_montant_bonus_jr    
                    FROM app_campagne_cible acc
                        join data_attribut att on acc.numero = att.numero
                        left join(
                        select abn.*, abd.valorisation, abd.dt_action from app_bonus abn 
                        join app_bonus_details abd on abn.id = abd.id_bonus
                        ) ab on ab.numero = acc.numero and ab.fk_id_campagne = acc.fk_id_campagne
                        WHERE acc.fk_id_campagne = $cmp and acc.numero in ($chaineMSISDN)
                        group by att.numero";
            }
            else
                $reqVerifNum = "SELECT att.numero, has_fidelity, 0 client_nbr_bonus, 0 client_montant_bonus, 0 client_nbr_bonus_jr, 0 client_montant_bonus_jr    
                    FROM app_campagne_cible acc
                        join data_attribut att on acc.numero = att.numero
                        WHERE acc.fk_id_campagne = $cmp and acc.numero in ($chaineMSISDN)";
            $result = $connection->query($reqVerifNum);

            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                $limitation_client = $limitation['client'];
                foreach ($limitation_client as $key => $val) {
                    if ((int) $cmp_lim[$cmp][$key] > 0)
                        $limitation_client[$key] = $cmp_lim[$cmp][$key] - $ligne->$key;
                }
                $msisdn = $ligne->numero;
                $hasFidelity = $ligne->has_fidelity;
                foreach ($arr_bonus[$msisdn] as $idx_bns => $arr_bnsGlob) {
                    if ($limitation_cmp['cmp_nbr_bonus_jr'] > 0 && $limitation_cmp['cmp_nbr_bonus'] > 0 &&
                            $limitation_client['client_nbr_bonus_jr'] > 0 && $limitation_client['client_nbr_bonus'] > 0) {

                        $arr_exp = explode('|', $idx_bns);
                        $req_ins = "insert into app_bonus (numero, fk_id_campagne, fk_id_groupe, dt_droit) 
                                values ('$msisdn', $cmp, " . $arr_exp[0] . ", '" . date('YmdHis') . "')";
                        $idBonus = 0;
                        foreach ($arr_bnsGlob as $idx => $bns) {
                            if ($bns['code'] == 1010101010 && !$hasFidelity)
                                continue;
                            if ($limitation_cmp['cmp_montant_bonus_jr'] >= $bns['valorisation'] && $limitation_cmp['cmp_montant_bonus'] >= $bns['valorisation'] &&
                                    $limitation_client['client_montant_bonus_jr'] >= $bns['valorisation'] && $limitation_client['client_montant_bonus'] >= $bns['valorisation']) {
                                if (!$idBonus) {
                                    $limitation_cmp['cmp_nbr_bonus_jr']--;
                                    $limitation_cmp['cmp_nbr_bonus']--;
                                    //echo "\n\r".$limitation_cmp['cmp_nbr_bonus'];
                                    $limitation_client['client_nbr_bonus_jr']--;
                                    $limitation_client['client_nbr_bonus']--;
                                    $connection->query($req_ins);
                                    $idBonus = $connection->lastInsertId('bonus_id_seq');
                                    //echo "\n\r Bonus pour le numéro : $msisdn";
                                }
                                $arr_req_bns_detail[] = "($idBonus," . $bns['idBns'] . ", " . $bns['nature'] . ", " . $bns['code'] . ", " . $bns['valeur'] . ", '" . $bns['dt_action'] . "', " . $bns['valorisation'] . ")";
                                $limitation_cmp['cmp_montant_bonus_jr'] -= $bns['valorisation'];
                                $limitation_cmp['cmp_montant_bonus'] -= $bns['valorisation'];
                                $limitation_client['client_montant_bonus_jr'] -= $bns['valorisation'];
                                $limitation_client['client_montant_bonus'] -= $bns['valorisation'];
                            } elseif ($limitation_cmp['cmp_montant_bonus'] <= 0 && $cmpStatus) {
                                $cmpStatus = false;
                                fn_stopCampagne($cmp, 'Montant Bonus Limite', $connection);
                            }
                        }
                    } elseif ($limitation_cmp['cmp_nbr_bonus'] <= 0 && $cmpStatus) {
                        $cmpStatus = false;
                        fn_stopCampagne($cmp, 'Nombre Bonus Limite', $connection);
                    }
                }
            }
            if (count($arr_req_bns_detail)) {
                $req_ins_det = 'insert into app_bonus_details (id_bonus, fk_id_bonus, fk_id_nature, code_bonus, valeur, dt_action, valorisation) 
                        values ' . implode(',', $arr_req_bns_detail);
                $result_det = $connection->query($req_ins_det);
            }
            if (!$cmpStatus)
                unset($grp_dec[$cmp]);
        }
    } catch (Exception $e) {
        throw ($e);
    }
}

function fn_stopCampagne($idCmp, $cause, $connection) {
    try {
        $result = $connection->query('SELECT ac.nbr_cible, ac.nbr_gc, ac.nom nom_cmp, ac.etat, chez_profil, profil_saisie ps, ru.id ru_id, ru.nom ru_nom, ru.prenom ru_prenom, ru.mail ru_mail
        FROM app_campagne ac 
        JOIN sys_users ru on createur = ru.id
        WHERE ac.id = ' . $idCmp);
        if ($result->rowCount()) {
            $li = $result->fetch(PDO::FETCH_OBJ);
            $nomCmp = $li->nom_cmp;
//        $chez = $li->chez_profil;
//        $ps = $li->ps;
            $nbrCible = $li->nbr_cible;
            $nbrGc = $li->nbr_gc;
            $createurNom = $li->ru_prenom . ' ' . $li->ru_nom;
            $createurMail = $li->ru_mail;
        }

        $body = "Bonjour $createurNom,<br>La campagne : ' ($nomCmp) , que vous avez créée est arrêter par : ROBOT TIMIRIS.<br>
                    Le motif d'arrêt est : $cause.<br>
                    <h4>TIMIRIS Plateforme</h4>";
        $subject = 'Arrêt campagne';
        $rqroi = $connection->query('select sum(valorisation) v_bonus from app_bonus_details abd JOIN app_bonus ab on ab.id = abd.id_bonus and ab.fk_id_campagne = ' . $idCmp);
        if ($rqroi->rowCount())
            $v_bonus = $rqroi->fetch(PDO::FETCH_OBJ)->v_bonus;
        else
            $v_bonus = 0;

        $rqroi = $connection->query('select cible, gc FROM app_campagne_kpi WHERE fk_id_campagne =' . $idCmp . " and tbname ='data_consommation_total'");
        $rqroi = $rqroi->fetch(PDO::FETCH_OBJ);
        $consCible = ($rqroi->cible / $nbrCible - (($nbrGc) ? $rqroi->gc / $nbrGc : 0)) * $nbrCible / 100;

        $roi = $consCible - $v_bonus;
        $connection->query('update app_campagne set etat = ' . CMP_ARRETEE . " , dt_fin_relle = '" . date('Y-m-d H:i:s') . "', roi = $roi");
        $res = $connection->query("insert into app_campagne_wf (fk_id_campagne, dt_action, id_profil, id_user, new_status, commentaire) 
                VALUES ($idCmp, '" . date('YmdHis') . "', 0, 0, " . CMP_ARRETEE . ", '" . str_replace("'", "''", $cause) . "')");
        if ($res && $createurMail != '') {
            $arr_cc = $arr_address = array();
            $arr_address[$createurMail] = $createurNom;
            $tbRetMail = sendMail($subject, $body, $arr_address, $arr_cc);
            if (!$tbRetMail['send'])
                echo $tbRetMail['message'] . "\n\r";
        }
    } catch (Exception $e) {
        throw ($e);
    }
}

//***************************************************************************

$reqLim = 'select id, cmp_nbr_bonus, cmp_montant_bonus, cmp_nbr_bonus_jr, cmp_montant_bonus_jr, client_nbr_bonus, client_montant_bonus,
                client_nbr_bonus_jr,client_montant_bonus_jr from app_campagne where etat = ' . CMP_ENCOURS;
$resLim = $connection->query($reqLim);
while ($li_lim = $resLim->fetch(PDO::FETCH_OBJ)) {
    $cmp_lim[$li_lim->id] = array(
        'cmp_nbr_bonus' => $li_lim->cmp_nbr_bonus,
        'cmp_montant_bonus' => $li_lim->cmp_montant_bonus,
        'cmp_nbr_bonus_jr' => $li_lim->cmp_nbr_bonus_jr,
        'cmp_montant_bonus_jr' => $li_lim->cmp_montant_bonus_jr,
        'cmp_total' => $li_lim->cmp_nbr_bonus + $li_lim->cmp_montant_bonus + $li_lim->cmp_nbr_bonus_jr + $li_lim->cmp_montant_bonus_jr,
        'client_nbr_bonus' => $li_lim->client_nbr_bonus,
        'client_montant_bonus' => $li_lim->client_montant_bonus,
        'client_nbr_bonus_jr' => $li_lim->client_nbr_bonus_jr,
        'client_montant_bonus_jr' => $li_lim->client_montant_bonus_jr,
        'client_total' => $li_lim->client_nbr_bonus + $li_lim->client_montant_bonus + $li_lim->client_nbr_bonus_jr + $li_lim->client_montant_bonus_jr
    );
}

$reqList = 'select distinct code_cmpt, valorisation from ref_compteurs';
$resList = $connection->query($reqList);
while ($li_list = $resList->fetch(PDO::FETCH_OBJ))
    $arr_valorisation[$li_list->code_cmpt] = $li_list->valorisation;
?>