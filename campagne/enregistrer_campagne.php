<?php

if (!isset($rep))
    $rep = "../";
if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
$tbRetour = array();
$tbRetour['exec'] = 0;
$tbRetour['message'] = '';
//echo json_encode($tbRetour);exit();
// ORDER BY type ASC, poids DESC
try {
    if (!isset($_POST["cmp"]))
        exit();
    require_once $rep . "conn/connection.php";
    require_once $rep . "nameTables.php";
    require_once $rep . "fn_formatter_date.php";
    require_once $rep . "lib/tbLibelle.php";
    require_once $rep . "ciblage/fn/fn_generateArrayParams.php";
    require_once $rep . "ciblage/fn/fn_getDateRel.php";
    $tbCmp = json_decode($_POST["cmp"], true);
    $tbGle = $tbCmp['glb'];
    $tbCbl = $tbCmp['cbl'];
    $tbBns = $tbCmp['bns'];
    $cmp_dt_from = ($tbGle['cmp_dt_from'] == '') ? 'NULL' : "'" . $tbGle['cmp_dt_from'] . "'";
    $cmp_dt_fin = $tbGle['cmp_dt_fin'];
    if ($tbGle['cmp_dt_from'] == '')
        $cdf = date('YmdHi');
    else {
        $cdf = str_replace('-', '', $tbGle['cmp_dt_from']);
        $cdf = str_replace(' ', '', $cdf);
        $cdf = str_replace(':', '', $cdf);
        $cdf = str_replace("'", '', $cdf);
//        $cdf = (int) $cdf;
    }
    $cdt = str_replace('-', '', $cmp_dt_fin);
    $cdt = str_replace(' ', '', $cdt);
    $cdt = str_replace(':', '', $cdt);
//    $cdt = (int) $cdt;
    if ($cdt <= $cdf) {
        $tbRetour['message'] = 'Date début ou fin incorrecte !!!';
        echo json_encode($tbRetour);
        exit();
    }
    $hasWl = $hasBl = 'false';
    $ar_lb = $ar_ln = array();
    $nbrCibleCmp = 0;
    if ($_FILES['idFLb']['name']) {
        if ($_FILES['idFLb']['error'])
            exit('Liste Blanche non chargée');
        else {
            $ar_lb = array_unique(file($_FILES['idFLb']['tmp_name']));
            foreach ($ar_lb as $k => $v) {
                $ar_lb[$k] = substr('222' . trim($v), -11);
            }
            $hasWl = 'true';
            $nbrCibleCmp = count($ar_lb);
        }
    }
    if ($_FILES['idFLn']['name']) {
        if ($_FILES['idFLn']['error'])
            exit('Liste noire non chargée');
        else {
            $ar_ln = array_unique(file($_FILES['idFLn']['tmp_name']));
            foreach ($ar_ln as $k => $v) {
                $ar_ln[$k] = substr('222' . trim($v), -11);
            }
            $hasBl = 'true';
        }
    }
    $connection->query('BEGIN');
    // Enregistrement de la cible
    if (!isset($tbCbl["cible_id"])) {
        $associationGroupe = $tbCbl["associationGroupe"];
        unset($tbCbl["associationGroupe"]);
        $tables = generateArrayParams($tbCbl);

        $req = 'INSERT INTO app_cibles (association_group, date_creation, fkid_user, cible) VALUES
				(\'' . $associationGroupe . '\', \'' . date('Y-m-d H:i:s') . '\', ' . $_SESSION["user"]["id"] . ', \'' . json_encode($tables) . '\')';
        $result = $connection->query($req);
        $idCible = $connection->lastInsertId('id_cible_seq');   //last inserit id
    } else
        $idCible = $tbCbl["cible_id"];

    // Enregistrement de la campagne
    $cmp_nom = str_replace("'", "''", $tbGle['cmp_nom']);
    $cmp_teasingAr = str_replace("'", "''", $tbGle['cmp_teasingAr']);
    $cmp_teasingFr = str_replace("'", "''", $tbGle['cmp_teasingFr']);
    $cmp_sms_bonusAr = str_replace("'", "''", $tbGle['cmp_sms_bonusAr']);
    $cmp_sms_bonusFr = str_replace("'", "''", $tbGle['cmp_sms_bonusFr']);
    $cmp_objectif = str_replace("'", "''", $tbGle['cmp_objectif']);
    $cmp_broadcast = $tbGle['cmp_broadcast'];
    $cmp_nbr_bonus = $tbGle['cmp_nbr_bonus'];
    $cmp_montant_bonus = $tbGle['cmp_montant_bonus'];
    $cmp_nbr_bonus_jr = $tbGle['cmp_nbr_bonus_jr'];
    $cmp_montant_bonus_jr = $tbGle['cmp_montant_bonus_jr'];
    $client_nbr_bonus = $tbGle['client_nbr_bonus'];
    $client_montant_bonus = $tbGle['client_montant_bonus'];
    $client_nbr_bonus_jr = $tbGle['client_nbr_bonus_jr'];
    $client_montant_bonus_jr = $tbGle['client_montant_bonus_jr'];
    $tpBonus = ($tbBns['type_dcl'] == '') ? 'NULL' : "'" . $tbBns['type_dcl'] . "'";
    $req = "INSERT INTO app_campagne (nom, dt_lancement, dt_fin, objectif, sms_ar, sms_fr, dt_creation, createur, id_cible, type_bonus, 
        profil_saisie, chez_profil, sms_bonus_ar, sms_bonus_fr, 
        cmp_nbr_bonus, cmp_montant_bonus, cmp_nbr_bonus_jr, cmp_montant_bonus_jr, client_nbr_bonus, 
        client_montant_bonus, client_nbr_bonus_jr, client_montant_bonus_jr, has_wl, has_bl, nbr_cible, broadcast) VALUES
        ('$cmp_nom', $cmp_dt_from, '$cmp_dt_fin', '$cmp_objectif', '$cmp_teasingAr', '$cmp_teasingFr', '" . date("YmdHis") . "', '" . $_SESSION["user"]["id"] . "', 
            $idCible, $tpBonus, '" . $_SESSION["user"]["profil"] . "', '" . $_SESSION["user"]["profil"] . "', '$cmp_sms_bonusAr', '$cmp_sms_bonusFr',
          $cmp_nbr_bonus,$cmp_montant_bonus,$cmp_nbr_bonus_jr,$cmp_montant_bonus_jr,$client_nbr_bonus,$client_montant_bonus,
            $client_nbr_bonus_jr,$client_montant_bonus_jr, $hasWl, $hasBl, $nbrCibleCmp, $cmp_broadcast)";
//    exit($req);
    $result = $connection->query($req);
    $idCmp = $connection->lastInsertId('campagne_id_seq');   //last inserit id
    // Enregistrement du Bonus
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
    if (count($ar_lb)) {
        $ar_lb = array_chunk($ar_lb, 1000);
        foreach ($ar_lb as $arlb) {
            $req = 'INSERT INTO app_campagne_cible (numero, fk_id_campagne, is_wl)
            VALUES (' . implode(",$idCmp, true),(", $arlb) . ",$idCmp, true)";
            $result = $connection->query($req);
        }
    }
    if (count($ar_ln)) {
        $ar_ln = array_chunk($ar_ln, 1000);
        foreach ($ar_ln as $arln) {
            $req = 'INSERT INTO app_campagne_exclus (numero, fk_id_campagne, is_bl)
            VALUES (' . implode(",$idCmp, true),(", $arln) . ",$idCmp, true)";
            $result = $connection->query($req);
        }
    }

    $req = "INSERT INTO app_campagne_wf (fk_id_campagne, dt_action, id_profil, id_user) 
                VALUES ($idCmp, '" . date('YmdHis') . "', " . $_SESSION['user']['profil'] . ", " . $_SESSION['user']['id'] . ")";
    $result = $connection->query($req);
    if ($connection->query('COMMIT')) {
        $tbRetour['exec'] = 1;
        $tbRetour['message'] = 'Campagne créée avec succès';
    } else
        throw("Impossible de faire COMMIT");
} catch (PDOException $e) {
    $connection->query('ROLLBACK');
    $tbRetour['message'] = $e->getMessage();
}
echo json_encode($tbRetour);
?>