<?php

$rep = '../../';
require_once $rep . "conn/connection.php";
$resVerif = $connection->query("SELECT * FROM sys_cron WHERE type = 'bonus' and etat = TRUE");
if (!$resVerif->rowCount())
    exit();
try {
    $maxSend = 5;
    if (date('Hi') == '0800')
        $maxSend = 100;
    require_once $rep . "defs.php";
    require_once $rep . "lib/tbLibelle.php";
    require_once $rep . "ws/timirisClient.php";
    require_once $rep . "fn_sendSMS.php";
    $reqBonus = "SELECT att.is_lang_ar, att.nom, att.points_fidelite, att.balance, ac.sms_bonus_ar, ac.sms_bonus_fr , ab.numero msisdn, 
                ab.id id_bns, abd.id id_bns_det, abd.fk_id_nature nature, abd.code_bonus code, abd.valeur valeur, abd.dt_action dt_action
            FROM app_campagne ac 
            JOIN app_bonus ab on ac.id = ab.fk_id_campagne and fk_id_groupe = 0
            JOIN app_bonus_details abd on ab.id = abd.id_bonus and abd.dt_bns is null and nbr_echec < $maxSend
            JOIN data_attribut att on att.numero = ab.numero
            UNION
            SELECT att.is_lang_ar, att.nom, att.points_fidelite, att.balance, acg.sms_bonus_ar , acg.sms_bonus_fr , ab.numero msisdn, 
                ab.id id_bns, abd.id id_bns_det, abd.fk_id_nature nature, abd.code_bonus code, abd.valeur valeur, abd.dt_action dt_action
            FROM app_campagne_groupe acg 
            JOIN app_bonus ab on acg.id = ab.fk_id_groupe
            JOIN app_bonus_details abd on ab.id = abd.id_bonus and abd.dt_bns is null and nbr_echec < $maxSend
            JOIN data_attribut att on att.numero = ab.numero
            order by id_bns";

    $resBonus = $connection->query($reqBonus);
    if ($resBonus->rowCount()) {
        $arrBns = $pNumbers = $bnsEchec = array();
        while ($liBns = $resBonus->fetch(PDO::FETCH_OBJ)) {
            if (!isset($arrBns[$liBns->id_bns]))
                $arrBns[$liBns->id_bns] = array('msisdn' => $liBns->msisdn, 'lang' => $liBns->is_lang_ar, 'sms_ar' => $liBns->sms_bonus_ar,
                    'sms_fr' => $liBns->sms_bonus_fr,
                    'nom' => $liBns->nom,
                    'balance' => (int) ($liBns->balance / 100),
                    'points_fidelite' => $liBns->points_fidelite,
                    'bns' => array()
                );
            $arrBns[$liBns->id_bns]['bns'][$liBns->id_bns_det] = array();
            $arrBns[$liBns->id_bns]['bns'][$liBns->id_bns_det]['nature'] = $liBns->nature;
            $arrBns[$liBns->id_bns]['bns'][$liBns->id_bns_det]['code'] = $liBns->code;
            $arrBns[$liBns->id_bns]['bns'][$liBns->id_bns_det]['valeur'] = $liBns->valeur;
            $arrBns[$liBns->id_bns]['bns'][$liBns->id_bns_det]['dtAction'] = $liBns->dt_action;
            if ($liBns->nature == BNS_FIDELITY)
                $pNumbers[$liBns->msisdn] = 1;
        }
        if (count($pNumbers)) {
            $reqVP = "select numero from data_point_fidelite_nombre_attribution where numero in ('" . implode("','", array_keys($pNumbers)) . "')";
            $pNumbers = $connection->query($reqVP)->fetchAll(PDO::FETCH_COLUMN);
        }
//    print_r($arrBns);

        foreach ($arrBns as $idBns => $bonus) {
            $msisdn = $bonus['msisdn'];
            $nom = $bonus['nom'];
            $solde = $bonus['balance'];
            $sfidelity = $bonus['points_fidelite'];
            $bns_values = array();
            if ($bonus['lang'] == 'true') {
                $sms = $bonus['sms_ar'];
                $enc = 2;
            } else {
                $sms = $bonus['sms_fr'];
//            $enc = (mb_detect_encoding($sms) == 'UTF-8') ? 2 : 0;
                $enc = 0;
            }
            $send = true;
            foreach ($bonus['bns'] as $id_bns_det => $bns) {
                echo date('YmdHis') . " $msisdn \r\n";
                $libCmpt = $libCompteur[$bns['nature'] . '_' . $bns['code']];
                switch ($bns['nature']) {
                    case BNS_SERVICE:
                        for ($i = 0; $i < $bns['valeur']; $i++) {
                            $response = fn_ActivateService($wsClientAppendante, $msisdn, $bns['code']);
                            echo "REQUEST:\n" . $wsClientAppendante->__getLastRequest() . "\n";
                            echo "Response:\n" . $wsClientAppendante->__getLastResponse() . "\n";
                        }
                        $bns_values[] = $bns['valeur'] . ' activation du service ' . $libCmpt;
                        break;
                    case BNS_FIDELITY:
                        $exist = (in_array($msisdn, $pNumbers)) ? 1 : 0;
                        echo "Bonus fidélity : " . $bns['valeur'] . " points \n\r";
                        $response = fnFidelityPoint($connection, $msisdn, $bns['valeur'], $bns['dtAction'], $exist);
                        $bns_values[] = $bns['valeur'] . ' points fidelite ';
                        if ($response)
                            $pNumbers[] = $msisdn;
                        break;
                    default:
                        $response = fn_AdjustAccount($wsClientAdjust, $msisdn, $bns['code'], $bns['valeur'], $MinMesure[$bns['nature']]);
                        echo "REQUEST:\n" . $wsClientAdjust->__getLastRequest() . "\n";
                        echo "Response:\n" . $wsClientAdjust->__getLastResponse() . "\n";
                        $bns_values[] = ($bns['valeur'] / $unitBns[$bns['nature']]['div']) . '  ' . $unitBns[$bns['nature']]['lib'];
                        break;
                }
                if (!$response) {
                    $send = false;
                    $bnsEchec[] = $id_bns_det;
                }
                else
                    $connection->query("update app_bonus_details set dt_bns = '" . date('YmdHis') . "' where id = $id_bns_det");
            }
            if ($send && $sms != '') {
                $bns_values = implode(', ', $bns_values);
                eval("\$sms = \"$sms\";");
                $ret = sendSMS($msisdn, $sms, 'PromoMattel', $enc, 'bonus');
                if ($ret == 1) {
                    $isl = ($bonus['lang']) ? 'true' : 'false';
                    $connection->query("UPDATE app_bonus SET dt_sms = '" . date('YmdHis') . "', is_lang_ar ='$isl' WHERE id = $idBns");
                }
            }
        }

        if (count($bnsEchec)) {
            $connection->query('UPDATE app_bonus_details set nbr_echec = nbr_echec +1 where id in (' . implode(', ', $bnsEchec) . ')');
        }
    }
} catch (Exception $e) {
    echo $e->getMessage() . "\n\r";
}
?>