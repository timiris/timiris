<?php

require_once "../fn_security.php";
check_session();
require '../lib/tbLibelle.php';
if (!isset($_POST["idCible"]) || empty($_POST["idCible"]))
    exit();
$idCible = $_POST["idCible"];
require_once "../conn/connection.php";
require_once "../fn_formatter_date.php";
header('Content-Encoding: UTF-8');
header('Content-Type: application/x-msexcel;charset=iso-8859-1');
header('Content-disposition: attachment; filename=Timiris_' . date('YmdHis') . '.csv');
if (isset($_POST["wl"]) && ($_POST["wl"] == 'btnBns')) {
    $rq = $connection->query('select fk_id_bonus, fk_id_nature, valeur, valorisation, code_bonus, numero, fk_id_groupe, dt_droit, dt_bns
        from app_bonus ab join app_bonus_details abd on ab.id = abd.id_bonus where fk_id_campagne = ' . (int) $_POST["idCible"]);
    echo "Numéro;Groupe vérifié;ID bonus;Valeur;Type bonus;Valorisation;Date droit bonus;Date Attribution\n";
    while ($ligne = $rq->fetch(PDO::FETCH_OBJ)) {
        echo $ligne->numero . ';' . $ligne->fk_id_groupe . ';' . $ligne->fk_id_bonus . ';' . $ligne->valeur . ';' .
        $libCompteur[$ligne->fk_id_nature . '_' . $ligne->code_bonus] . ';' . $ligne->valorisation . ';' . formatter_date($ligne->dt_droit) . ';' . formatter_date($ligne->dt_bns) . "\n";
    }
    exit();
}

$cnd = '';
if (isset($_POST["wl"])) {
    switch ($_POST["wl"]) {
        case 'btnBl':
            $tbName = 'app_campagne_exclus';
            $cnd = ' and is_bl = true ';
            break;
        case 'btnWl':
            $tbName = 'app_campagne_cible';
            $cnd = ' and is_wl = true ';
            break;
        case 'btnGt':
            $tbName = 'app_campagne_exclus';
            $cnd = ' and is_bl = false ';
            break;
        default:exit();
    }
}
$_SESSION['ciblage'] = 'en_cours';
//récupération des champs des tables

$tbRetour['exec'] = 0;
$tbRetour['cible'] = 0;
$tbRetour['parc'] = 0;
$tbRetour['message'] = '';

// ORDER BY type ASC, poids DESC
try {
    $rqJOIN = "SELECT numero FROM $tbName WHERE fk_id_campagne = $idCible $cnd";
    $req = "SELECT att.points_fidelite,att.balance,att.numero,dt_active,genre,rp.libelle as rp_lib,nom,re.libelle as re_lib,dt_active_stop,dt_suspend_stop
                FROM data_attribut att
                JOIN ref_liste_choix_attribut rp ON att.profil::varchar= rp.code and rp.attribut = 'profil' 
                JOIN ref_etat_ligne_in re ON att.status = re.id
                JOIN (" . $rqJOIN . ") res on res.numero=att.numero";
    $result = $connection->query($req);
    if ($result->rowCount()) {
        echo "Numero;Nom;Genre;Profil;Status;Balance;Points;Date activation;Date suspend;Date Disable\n";
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            $genre = strtolower($ligne->genre == 'f') ? 'Femme' : 'Homme';
            echo $ligne->numero . ';' . $ligne->nom . ';' . $genre . ';' . $ligne->rp_lib . ';' . $ligne->re_lib
            . ';' . ($ligne->balance / 100) . ';' . $ligne->points_fidelite
            . ';' . formatter_date($ligne->dt_active) . ';' . formatter_date($ligne->dt_active_stop) . ';' . formatter_date($ligne->dt_suspend_stop) . "\n";
        }
        $_SESSION['ciblage'] = 'finished';
    }
    else
        echo "Aucune linge retournée";
    exit();
    $tbRetour['exec'] = 1;
} catch (PDOException $e) {
    $tbRetour['message'] = $e->getMessage();
}
$_SESSION['ciblage'] == 'finished';
echo json_encode($tbRetour);

// require_once 'result_ciblage.php';
?>