<?php

$rep = '/tim_arch/dump/fidelity/';
require '../connection.php';
$dateJ = strtotime(date("Ymd"));
$dtJD = date("Y-m-d", strtotime("-1 days", $dateJ));
$fileName = $rep . 'dump_fidelity_' . $dtJD . '.txt';
if (is_file($fileName) || is_file($fileName . '.gz'))
    exit('exist deja');
$req = $connection->query("select champ from historique_correspondance where h_date = '$dtJD'");
if ($req->rowCount()) {
    $res = $req->fetch(PDO::FETCH_OBJ);
    $codej = $res->champ;
    $req = 'select att.numero, att.points_fidelite solde, case when cns.' . $codej . ' is null then 0 else cns.' . $codej . ' end as cns,  
                    case when at.' . $codej . ' is null then 0 else at.' . $codej . ' end as atr 
                from data_attribut att
                left join data_point_fidelite_valeur_attribution at on at.numero = att.numero
                left join data_point_fidelite_valeur_consommation cns on cns.numero = att.numero
                where has_fidelity > 0';
    $res = $connection->query($req);
    $fp = fopen($fileName, 'w');
    fputs($fp, 'msisdn|solde_fidelite|attribution|consommation' . "\r\n");
    while ($li = $res->fetch(PDO::FETCH_OBJ)) {
        fputs($fp, $li->numero . '|' . $li->solde . '|' . $li->atr . '|' . $li->cns . "\r\n");
    }
    fclose($fp);
}
?>
