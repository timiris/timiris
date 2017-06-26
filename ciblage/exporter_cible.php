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
        $req_global = generateRequete('exp', $tables, $associationGroupe);
        $tbRetour['req'] = $req_global;
        $result = $connection->query($req_global);
        header('Content-Type: application/x-msexcel');
        header('Content-disposition: attachment; filename=Timiris_' . date('YmdHis') . '.csv');
        if ($result->rowCount()) {
            echo "Numero;Nom;Genre;Profil;Status;Balance;Points;Date activation;Date suspend;Date Disable\n";
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                $genre = strtolower($ligne->genre == 'f') ? 'Femme' : 'Homme';
                echo $ligne->numero . ';' . $ligne->nom . ';' . $genre . ';' . $ligne->rp_lib . ';' . $ligne->re_lib
                . ';' . ($ligne->balance / 100) . ';' . $ligne->points_fidelite
                . ';' . formatter_date($ligne->dt_active) . ';' . formatter_date($ligne->dt_active_stop) . ';' . formatter_date($ligne->dt_suspend_stop) . "\n";
            }
        }
        else
            echo "Aucune linge retournée";
        $_SESSION['ciblage'] = 'finished';
        exit();
    }
    $tbRetour['exec'] = 1;
} catch (PDOException $e) {
    $tbRetour['message'] = $e->getMessage();
}
$_SESSION['ciblage'] == 'finished';
echo json_encode($tbRetour);

// require_once 'result_ciblage.php';
?>