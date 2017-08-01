<?php

if (!isset($rep))
    $rep = '../';
require_once $rep . "fn_security.php";
check_session();
$_SESSION['ciblage'] = 'en_cours';
//récupération des champs des tables
require_once $rep . "conn/connection.php";
require_once $rep . "nameTables.php";
require_once $rep . "fn_formatter_date.php";
require_once $rep . "lib/tbLibelle.php";
// require_once "fn/fn_decode_ciblage.php";
require_once "fn/fn_generateArrayParams.php";
require_once "fn/fn_generateRequete.php";
require_once "fn/fn_getDateRel.php";

$tbRetour['exec'] = 0;
$tbRetour['cible'] = 0;
$tbRetour['parc'] = 0;
$tbRetour['message'] = '';

// ORDER BY type ASC, poids DESC
try {
    $tab_params = json_decode($_POST["parms"], true);
    if (!count($tab_params))
        exit('Argument non valide');
    $cibleName = '';
    if (isset($tab_params["cibleName"])) {
        $cibleName = $tab_params["cibleName"];
        unset($tab_params["cibleName"]);
    }

    $tp_dmd = $tab_params["tp_dmd"];
    unset($tab_params["tp_dmd"]);
    $associationGroupe = $tab_params["associationGroupe"];
    unset($tab_params["associationGroupe"]);
    if (isset($tab_params["cibleId"])) {
        $cibleId = $tab_params["cibleId"];
        unset($tab_params["cibleId"]);
    }
    $tables = generateArrayParams($tab_params);
    if ($tp_dmd == 'enregistrer') {
        $cibleName = str_replace("'", "''", $cibleName);
        $req = "INSERT INTO app_cibles (nom, association_group, date_creation, fkid_user, cible)  VALUES
		('" . $cibleName . "', '" . $associationGroupe . "', '" . date('Y-m-d H:i:s') . "', 
                    " . $_SESSION["user"]["id"] . ", '" . json_encode($tables) . "')";
        $result = $connection->query($req);
        echo '<p align=center style = "color:blue; font-size:150%">Enregistrement fait avec succes !!!</p>';
        exit();
    } elseif ($tp_dmd == 'enregistrer_cible_camp') {
        $req = 'INSERT INTO app_cibles (association_group, date_creation, fkid_user, cible)  VALUES
                (\'' . $associationGroupe . '\', \'' . date('Y-m-d H:i:s') . '\',
                    ' . $_SESSION["user"]["id"] . ', \'' . json_encode($tables) . '\')';
        exit();
    } 

    $req_global = generateRequete($tp_dmd, $tables, $associationGroupe);
//    echo $req_global;
    if ($tp_dmd == 'exp') { // Export
        $tbRetour['req'] = $req_global;
        $result = $connection->query($req_global);
        header('Content-Encoding: latin1');
//        header('Content-Type: text/csv; charset=utf-8' );
        header('Content-Type: application/x-msexcel; charset=latin1');
        header('Content-disposition: attachment; filename=Timiris_' . date('YmdHis') . '.csv');
        echo "Numero;Nom;Genre;Profil;Status;Date activation;Date suspend;Date Disable\n";
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            $genre = strtolower($ligne->genre == 'f') ? 'Femme' : 'Homme';
            echo $ligne->numero . ';' . $ligne->nom . ';' . $genre . ';' . $ligne->rp_lib . ';' . $ligne->re_lib
            . ';' . formatter_date($ligne->dt_active) . ';' . formatter_date($ligne->dt_active_stop) . ';' . formatter_date($ligne->dt_suspend_stop) . "\n";
        }
        $_SESSION['ciblage'] = 'finished';
        exit();
    } else {  // Calcul ciblage
        $parc = $cible = 0;
        $req_parc = 'SELECT count(numero) as nbrparc from data_attribut WHERE profil!=333333 and status != -1';
        $tbRetour['req'] = $req_global;
//        echo $req_global;
        $result = $connection->query($req_global);
        if ($result->rowCount()) {
            $ligne = $result->fetch(PDO::FETCH_OBJ);
            $tbRetour['cible'] = $ligne->nbrcible;
            $cible = $ligne->nbrcible;
        }
        $result = $connection->query($req_parc);
        if ($result->rowCount()) {
            $ligne = $result->fetch(PDO::FETCH_OBJ);
            $tbRetour['parc'] = $ligne->nbrparc;
            $parc = $ligne->nbrparc;
        }
        include 'result_ciblage.php';
    }
    $tbRetour['exec'] = 1;
} catch (PDOException $e) {
    $tbRetour['message'] = $e->getMessage();
}
$_SESSION['ciblage'] = 'finished';
//echo json_encode($tbRetour);
// require_once 'result_ciblage.php';
?>