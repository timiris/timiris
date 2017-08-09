<?php

if (!isset($_SESSION))
    session_start();
if (!isset($rep))
    $rep = "../../";
require_once $rep . "fn_security.php";
check_session();
if (isset($_POST['idCmp'])) {
    require_once $rep . "defs.php";
    require_once $rep . "conn/connection.php";
    require_once $rep . "campagne/fn/drawBonus.php";
//    require_once "../lib/lib.php";
    require_once $rep . "lib/tbLibelle.php";
    $idCmp = (int) $_POST['idCmp'];
    $cmp = $connection->query('SELECT * FROM app_campagne WHERE id = ' . $idCmp);
    if ($cmp->rowCount()) {
        $liCmp = $cmp->fetch(PDO::FETCH_OBJ);
        $defDeclencheur = $liCmp->type_bonus;
        require_once $rep . "campagne/declencheur/declencheur.php";
        if ($defDeclencheur != '') {
            $sms_bonus_ar = $liCmp->sms_bonus_ar;
            $sms_bonus_fr = $liCmp->sms_bonus_fr;
            $rqGrp = $connection->query("SELECT acg.id as idgrp, acd.id as iddec, * "
                    . " FROM app_campagne_groupe acg "
                    . " JOIN app_campagne_declencheur acd on acg.id = acd.fk_id_groupe "
                    . " WHERE fk_id_campagne = $idCmp");
            $arrGrp = $arrBns = array();
            while ($grp = $rqGrp->fetch(PDO::FETCH_OBJ)) {
                $idGrp = $grp->idgrp;
                $idDec = $grp->iddec;
                if (!isset($arrGrp[$idGrp]))
                    $arrGrp[$idGrp] = array('nature' => $grp->fk_id_nature, 'sms_ar' => $grp->sms_bonus_ar, 'sms_fr' => $grp->sms_bonus_fr, 'declencheur' => array());
                $arrGrp[$idGrp]['declencheur'][$idDec] = array('code_declencheur' => $grp->code_declencheur, 'operateur' => $grp->operateur,
                    'valeur' => $grp->valeur, 'unite' => $grp->unite, 'fk_id_td' => $grp->fk_id_td, 'fk_id_td_event' => $grp->fk_id_td_event);
            }
            $rqBonus = $connection->query("SELECT * FROM app_campagne_bonus WHERE fk_id_campagne = $idCmp");
            while ($bns = $rqBonus->fetch(PDO::FETCH_OBJ)) {
                if (!isset($arrBns[$bns->fk_id_groupe]))
                    $arrBns[$bns->fk_id_groupe] = array();
                $arrBns[$idGrp][$bns->id] = array('type_bonus' => $bns->type_bonus, 'nature' => $bns->nature,
                    'code_bonus' => $bns->code_bonus, 'valeur' => $bns->valeur, 'ch_ref' => $bns->ch_ref,
                    'unite' => $bns->unite);
            }
            
            drawBonus($idCmp, $defDeclencheur, $arrGrp, $arrBns, $connection);
        }
    }
}
?>