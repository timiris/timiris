<?php

require_once "../fn_security.php";
check_session();
require '../lib/tbLibelle.php';
if (!isset($_POST["idCible"]) || empty($_POST["idCible"]))
    exit();
$idCmp = $_POST["idCible"];
require_once "../conn/connection.php";
require_once "../fn_formatter_date.php";
header('Content-Encoding: UTF-8');
header('Content-Type: application/x-msexcel;charset=utf-8');
header('Content-disposition: attachment; filename=Timiris_' . date('YmdHis') . '.csv');

try {
    $rqJOIN = "SELECT numero FROM app_campagne_cible WHERE fk_id_campagne = $idCmp ";
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
        echo "Cible Vide";
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>