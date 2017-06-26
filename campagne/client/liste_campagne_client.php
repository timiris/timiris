<?php
if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
?>
<table id = 'dataTable' class=" dataTable display" cellspacing="0" width="100%">
    <thead>
        <tr align ="left"><th>ID</th><th>Nom </th><th>date création</th><th>date fin</th><th>Etat</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php
        require_once "../conn/connection.php";
        require_once '../fn_formatter_date.php';
        $result = $connection->query('select * from ref_etat_campagne');
        $etat_cmp = array();
        while ($ligne = $result->fetch(PDO::FETCH_OBJ))
            $etat_cmp[$ligne->id] = $ligne->libelle;
        $req = "SELECT ac.id as ac_id, ac.nom as ac_nom, dt_lancement, ac.etat as etat_cmp, dt_fin, dt_creation, ru.nom as ru_nom, prenom 
		FROM app_campagne ac
                JOIN app_campagne_cible acc on acc.fk_id_campagne=ac.id and acc.numero = '$numero'
                JOIN sys_users ru on ac.createur = ru.id 
                WHERE ac.etat >= " . CMP_ENCOURS;
        $result = $connection->query($req);
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            echo "<tr id = 'ligne_campagne_" . $ligne->ac_id . "'>
                    <td>" . $ligne->ac_id . "</td>
                    <td>" . $ligne->ac_nom . "</td>
                    <td>" . formatter_date($ligne->dt_creation) . "</td>
                    <td>" . $ligne->dt_fin . "</td>
                    <td>" . $etat_cmp[$ligne->etat_cmp] . "</td>
                    <td><input type = 'button' style = 'width:80px; font-size : 70%; font-weight:bold; z-index:5;' class = 'aff_det_cmp_client button12 blue' value = 'Afficher' name = 'cmp" . $ligne->ac_id . "'></td>
            </tr>";
        }
        ?>
    </tbody>

</table>
<br><br>
<script>
    fnDataTable(0, "desc", "campagne");
</script>