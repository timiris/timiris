<?php

function save_gle($connection, array $params) {
    try {
        $idCmp = $params['idCmp'];
        $connection->query("delete from app_campagne_cible where fk_id_campagne = $idCmp");
        $connection->query("delete from app_campagne_exclus where fk_id_campagne = $idCmp");

        $cmp_nom = str_replace("'", "''", $params['cmp_nom']);
        $cmp_dt_from = ($params['cmp_dt_from'] == '') ? 'NULL' : "'" . $params['cmp_dt_from'] . "'";
        $cmp_dt_fin = $params['cmp_dt_fin'];
        $cmp_teasingAr = str_replace("'", "''", $params['cmp_teasingAr']);
        $cmp_teasingFr = str_replace("'", "''", $params['cmp_teasingFr']);
        $cmp_objectif = str_replace("'", "''", $params['cmp_objectif']);
        $cmp_broadcast = $params['cmp_broadcast'];
        $nc = $params['nc'];
        $hb = $params['hb'];
        $hw = $params['hw'];
        $req = "update app_campagne set nom = '$cmp_nom', dt_lancement = $cmp_dt_from, dt_fin = '$cmp_dt_fin', objectif = '$cmp_objectif', 
        sms_ar = '$cmp_teasingAr',  sms_fr = '$cmp_teasingFr', 
            has_wl = $hw, has_bl = $hb, nbr_cible = $nc, broadcast = $cmp_broadcast WHERE id = $idCmp";

        $connection->query($req);
        if ($hw == 'true') {
            $req = 'insert into app_campagne_cible (numero, fk_id_campagne, is_wl)
            VALUES (' . implode(",$idCmp, true),(", $params['wl']) . ",$idCmp, true)";
            $result = $connection->query($req);
        }
        if ($hb == 'true') {
            $req = 'insert into app_campagne_exclus (numero, fk_id_campagne, is_bl)
            VALUES (' . implode(",$idCmp, true),(", $params['bl']) . ",$idCmp, true)";
            $result = $connection->query($req);
        }
    } catch (Exception $e) {
        throw($e);
    }
}

if (!isset($rep))
    $rep = "../";
if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
$tbRetour = array();
$tbRetour['exec'] = 0;
$tbRetour['message'] = '';
if (!isset($_POST["cmp"]))
    exit();
require_once $rep . "conn/connection.php";
require_once $rep . "nameTables.php";
require_once $rep . "fn_formatter_date.php";
require_once $rep . "lib/tbLibelle.php";
require_once $rep . "ciblage/fn/fn_generateArrayParams.php";
require_once $rep . "ciblage/fn/fn_getDateRel.php";
$tbCmp = json_decode($_POST["cmp"], true);
try {
    $connection->query('BEGIN');

    if ($tbCmp['dmd'] == 'gle') {
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
        $tbCmp['bl'] = $ar_ln;
        $tbCmp['wl'] = $ar_lb;
        $tbCmp['hb'] = $hasBl;
        $tbCmp['hw'] = $hasWl;
        $tbCmp['nc'] = $nbrCibleCmp;
        save_gle($connection, $tbCmp);
        if ($connection->query('COMMIT')) {
            $tbRetour['exec'] = 1;
            $tbRetour['message'] = 'Campagne modifiée avec succès';
        } else
            throw ("COMMIT Not Worked ");
    }elseif ($tbCmp['dmd'] == 'budget') {
        $idCmp = (int) $tbCmp['idCmp'];
        unset($tbCmp['idCmp']);
        unset($tbCmp['dmd']);
        foreach ($tbCmp as $key => $val) {
            $tbCmp[$key] = $key . ' = ' . (int) $val;
        }
        $connection->query('UPDATE app_campagne SET ' . implode(',', $tbCmp) . ' WHERE id = ' . $idCmp);
        if ($connection->query('COMMIT')) {
            $tbRetour['exec'] = 1;
            $tbRetour['message'] = 'Campagne modifiée avec succès';
        } else
            throw ("COMMIT Not Worked ");
    }elseif ($tbCmp['dmd'] == 'ciblage') {
        $idCmp = (int) $tbCmp['idCmp'];
        unset($tbCmp['idCmp']);
        unset($tbCmp['dmd']);
        if ($tbCmp['idChoixCible'] == 'idComposerCible') {
            $cible = $tbCmp['cible'];
            $associationGroupe = $cible["associationGroupe"];
            unset($cible["associationGroupe"]);
            if (isset($cible["cibleId"]))
                unset($cible["cibleId"]);
            $tables = generateArrayParams($cible);
            $req = 'INSERT INTO app_cibles (association_group, date_creation, fkid_user, cible) VALUES
				(\'' . $associationGroupe . '\', \'' . date('Y-m-d H:i:s') . '\', ' . $_SESSION["user"]["id"] . ', \'' . json_encode($tables) . '\')';
            $result = $connection->query($req);
            $idCible = $connection->lastInsertId('id_cible_seq');
        } elseif ($tbCmp['idChoixCible'] == 'idChosirCible') {
            $idCible = (int) $tbCmp['idCible'];
        } else
            $idCible = 0;
        $connection->query('UPDATE app_campagne SET id_cible = ' . $idCible . ' WHERE id = ' . $idCmp);
        if ($connection->query('COMMIT')) {
            $tbRetour['exec'] = 1;
            $tbRetour['message'] = 'Campagne modifiée avec succès';
        } else
            throw ("COMMIT Not Worked ");
    }
} catch (Exception $e) {
    $connection->query('ROLLBACK');
    $tbRetour['message'] = $e->getMessage();
}
echo json_encode($tbRetour);
?>