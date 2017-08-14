<?php

if (isset($_POST['parms'])) {
    try {
        $tbRetour = array();
        $tbRetour['exec'] = 0;
        $tbRetour['message'] = '';
        if (!isset($rep))
            $rep = "../../";
        require_once $rep . "conn/connection.php";
        $tbBns = json_decode($_POST['parms'], true);
        $idCmp = (int) $tbBns['id_cmp'];
        $smsAr = str_replace("'", "''", $tbBns['cmp_sms_bonusAr']);
        $smsFr = str_replace("'", "''", $tbBns['cmp_sms_bonusFr']);
        $connection->beginTransaction();
        $connection->query("delete from app_campagne_declencheur where fk_id_groupe in( select id from app_campagne_groupe where fk_id_campagne=$idCmp)");
        $connection->query("delete from app_campagne_groupe where fk_id_campagne=$idCmp");
        $connection->query("delete from app_campagne_bonus where fk_id_campagne=$idCmp");
        $connection->query("UPDATE app_campagne SET sms_bonus_ar = '$smsAr', sms_bonus_fr = '$smsFr' where id=$idCmp");
        if ($tbBns['type_dcl'] != '') {
            $bnsGlb = $tbBns['bnsGlb'];
            if (count($bnsGlb)) { // Nous avons un bonus global
                $idGrp = 0;
                $vls = array();
                foreach ($bnsGlb as $k => $bonus) {
                    $vls[] = '(' . $idCmp . ',' . $idGrp . ', ' . $bonus['type'] . ', ' . $bonus['nature'] . ', ' . $bonus['code_bonus'] . ', ' . $bonus['valeur'] . ', \'' . $bonus['ch_ref'] . '\', ' . $bonus['unite'] . ')';
                }
                $req = "INSERT INTO app_campagne_bonus(fk_id_campagne, fk_id_groupe, type_bonus, nature, code_bonus, valeur, ch_ref, unite) VALUES " .
                        implode(', ', $vls);
                $result = $connection->query($req);
            }
            if ($tbBns['type_dcl'] != 'fidelite') {
                $groupes = $tbBns['dcl_grp'];
                foreach ($groupes as $k => $grp) {
                    $req = "INSERT INTO app_campagne_groupe(fk_id_campagne, fk_id_nature, sms_bonus_ar, sms_bonus_fr) 
                    VALUES ($idCmp, " . $grp['nature'] . ",'" . str_replace("'", "''", $grp['sms_bonusAr']) . "','" . str_replace("'", "''", $grp['sms_bonusFr']) . "')";
                    $result = $connection->query($req);
                    $idGrp = $connection->lastInsertId('cmp_grp_id_seq');   //last inserit id
                    foreach ($grp['dcl'] as $k => $dcl) {
                        if (is_array($dcl['valeur']))
                            $dcl['valeur'] = implode('|', $dcl['valeur']);
                        if (!isset($dcl['unite']))
                            $dcl['unite'] = 0;
                        if ($tbBns['type_dcl'] == 'evenement') {
                            $tdEvent = $dcl['type_donnee'];
                            $tdT = 0;
                        } else {
                            $tdEvent = 0;
                            $tdT = $dcl['type_donnee'];
                        }
                        $req = "INSERT INTO app_campagne_declencheur(fk_id_groupe, code_declencheur, operateur, valeur, unite, fk_id_td, fk_id_td_event) 
                        VALUES ($idGrp, '" . $dcl['code'] . "', '" . $dcl['operateur'] . "', '" . $dcl['valeur'] . "', " . $dcl['unite'] . ", $tdT, $tdEvent)";
                        $result = $connection->query($req);
                    }
                    if (count($grp['bns'])) {
                        $vls = array();
                        foreach ($grp['bns'] as $k => $bonus) {
                            if ($bonus['type'] == 2) {  // proportionnel
                                $bonus['ch_ref'] = $bonus['unite'];
                                $bonus['unite'] = 1;
                            }
                            $vls[] = '(' . $idCmp . ',' . $idGrp . ', ' . $bonus['type'] . ', ' . $bonus['nature'] . ', ' . $bonus['code_bonus'] . ', ' . $bonus['valeur'] . ', \'' . $bonus['ch_ref'] . '\', ' . $bonus['unite'] . ')';
                        }
                        $req = "INSERT INTO app_campagne_bonus(fk_id_campagne, fk_id_groupe, type_bonus, nature, code_bonus, valeur, ch_ref, unite) VALUES " .
                                implode(', ', $vls);
                        $result = $connection->query($req);
                    }
                }
            }
        }
        $connection->commit();
        $tbRetour['exec'] = 1;
    } catch (Exception $e) {
        $connection->rollBack();
        $tbRetour['message'] = $e->getMessage();
    }
    echo json_encode($tbRetour);
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

