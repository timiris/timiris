<?php
if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
?>
<table id = 'dataTable' class=" dataTable display" cellspacing="0" width="100%">
    <thead>
        <tr align ="left"><th>id</th><th>Nom </th><th>date création</th><th>date lancement</th><th>date fin</th><th>Créateur</th><th>Etat</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php
        require_once "../conn/connection.php";
        require_once '../fn_formatter_date.php';
        $req = 'select * from ref_etat_campagne';
        $result = $connection->query($req);
//        $etat_cmp = array(1 => 'Edition', 2 => 'Soumise', 3 => 'En cours', 4 => 'Terminée', 5 => 'Arrêtée', 6 => 'Rejetée', 7 => 'Suspendue', 8 => 'Validée N1', 9 => 'Validée N2', 10 => 'Validée N3', 11 => 'Validée N4');
        $etat_cmp = array();
        while ($ligne = $result->fetch(PDO::FETCH_OBJ))
            $etat_cmp[$ligne->id] = $ligne->libelle;
        $req = "SELECT ac.id as ac_id, ac.nom as ac_nom, dt_lancement, dt_lancement_reelle, ac.etat as etat_cmp, dt_fin, dt_creation, ru.nom as ru_nom, prenom 
		FROM app_campagne ac JOIN sys_users ru on ac.createur = ru.id WHERE ac.etat != 0";
        $result = $connection->query($req);
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            $dtStart = ($ligne->dt_lancement!='')? $ligne->dt_lancement: ($ligne->dt_lancement_reelle !='')?$ligne->dt_lancement_reelle:'à la validation';
            echo "<tr id = 'ligne_campagne_" . $ligne->ac_id . "'>
                    <td>" . $ligne->ac_id . "</td>
                    <td>" . $ligne->ac_nom . "</td>
                    <td>" . formatter_date($ligne->dt_creation) . "</td>
                    <td>" . $dtStart. "</td>
                    <td>" . $ligne->dt_fin . "</td>
                    <td>" . $ligne->ru_nom . " " . $ligne->prenom . "</td>
                    <td>" . $etat_cmp[$ligne->etat_cmp] . "</td>
                    <td><input type = 'button' class = 'afficher_det_cmp button12 blue actionList' value = 'Afficher' name = 'cmp" . $ligne->ac_id . "'></td>
            </tr>";
        }
        ?>
    </tbody>

</table>
<br><br>
<script>
    fnDataTable(0, "desc", "campagne");
</script>