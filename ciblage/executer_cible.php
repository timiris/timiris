<?php

require_once "../fn_security.php";
check_session();
if (!isset($_POST["idCible"]) || empty($_POST["idCible"]))
    exit();
$idCible = $_POST["idCible"];
$_SESSION['ciblage'] = 'en_cours';
//récupération des champs des tables
require_once "../conn/connection.php";
require_once "../nameTables.php";
require_once "../fn_formatter_date.php";
require_once "../lib/tbLibelle.php";
require_once "fn/fn_decode_ciblage.php";
require_once "fn/fn_generateRequete.php";
require_once "fn/fn_getDateRel.php";

$tbRetour['exec'] = 0;
$tbRetour['cible'] = 0;
$tbRetour['parc'] = 0;
$tbRetour['message'] = '';

// ORDER BY type ASC, poids DESC
try {
    $req = 'SELECT * FROM app_cibles WHERE id = ' . $idCible;
    $result = $connection->query($req);
    if ($result->rowCount()) {
        $tbRetour['req'] = $req;
        $ligne = $result->fetch(PDO::FETCH_OBJ);
        $associationGroupe = $ligne->association_group;
        $tables = json_decode($ligne->cible, true);
        $req_global = generateRequete('', $tables, $associationGroupe);
//        exit($req_global);
        $result = $connection->query($req_global);
        $tbRetour['req'] = $req_global;
//        $result = $connection->query($req_global);
        $cible = $parc = 0;
        if ($result->rowCount()) {
            $ligne = $result->fetch(PDO::FETCH_OBJ);
            $cible = $ligne->nbrcible;
        }
        $req_parc = 'SELECT count(numero) as nbrparc from data_attribut where profil != 333333 and status != -1';
        $result = $connection->query($req_parc);
        if ($result->rowCount()) {
            $ligne = $result->fetch(PDO::FETCH_OBJ);
            $parc = $ligne->nbrparc;
        }

        require 'result_ciblage.php';
    }
    $tbRetour['exec'] = 1;
} catch (PDOException $e) {
    $tbRetour['message'] = $e->getMessage();
}
?>