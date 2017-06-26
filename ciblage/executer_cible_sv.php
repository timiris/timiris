<?php

require_once "../fn_security.php";
check_session();
if (!isset($_POST["idCmp"]) || empty($_POST["idCmp"]))
    exit();
$idCmp = $_POST["idCmp"];
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
    $req = 'SELECT count(*) nbrcible FROM app_campagne_cible WHERE fk_id_campagne = ' . $idCmp;
    $result = $connection->query($req);
    $ligne = $result->fetch(PDO::FETCH_OBJ);
    $cible = $ligne->nbrcible;
    $req_parc = 'SELECT count(numero) as nbrparc from data_attribut where profil != 333333 and status != -1';
    $result = $connection->query($req_parc);
    $ligne = $result->fetch(PDO::FETCH_OBJ);
    $parc = $ligne->nbrparc;
    require 'result_ciblage.php';
    $tbRetour['exec'] = 1;
} catch (PDOException $e) {
    $tbRetour['message'] = $e->getMessage();
}
?>