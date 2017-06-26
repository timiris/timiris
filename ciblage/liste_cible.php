<?php
if (!isset($rep))
    $rep = '../';
require_once $rep . "fn_security.php";
check_session();
?>
<table id = 'dataTable' class=" dataTable display" cellspacing="0" width="95%">
    <thead>
        <tr align ="left"><th>id</th><th>Nom de la cible</th><th>date création</th><th>Créateur</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php
        require_once $rep . "conn/connection.php";
        $req = "SELECT ac.id as ac_id, ac.nom as ac_nom, date_creation, ru.nom as ru_nom, prenom 
		FROM app_cibles ac JOIN sys_users ru on ac.fkid_user = ru.id WHERE ac.etat = 1 and ac.nom is not null";
        $result = $connection->query($req);
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            echo "<tr id = 'ligne_cible_" . $ligne->ac_id . "'>
                    <td>" . $ligne->ac_id . "</td>
                    <td>" . $ligne->ac_nom . "</td>
                    <td>" . $ligne->date_creation . "</td>
                    <td>" . $ligne->ru_nom . " " . $ligne->prenom . "</td>
                    <td><input type = 'button' class = 'afficher_det_cible button12 blue actionList' value = 'Afficher' name = 'cible" . $ligne->ac_id . "'></td>
            </tr>";
        }
        ?>
    </tbody>
</table>
<br/><br/>
<script>
    fnDataTable(1, "asc", "cible");
</script>