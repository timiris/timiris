<?php
if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
if (isset($_POST['idCmp'])) {
    require_once "../conn/connection.php";
    $cmp = $connection->query('SELECT * FROM app_campagne WHERE id = ' . (int) $_POST['idCmp']);
    if ($cmp->rowCount()) {
        $liCmp = $cmp->fetch(PDO::FETCH_OBJ);
        $cmp_nbr_bonus = $liCmp->cmp_nbr_bonus;
        $cmp_montant_bonus = $liCmp->cmp_montant_bonus;
        $cmp_nbr_bonus_jr = $liCmp->cmp_nbr_bonus_jr;
        $cmp_montant_bonus_jr = $liCmp->cmp_montant_bonus_jr;
        $client_nbr_bonus = $liCmp->client_nbr_bonus;
        $client_montant_bonus = $liCmp->client_montant_bonus;
        $client_nbr_bonus_jr = $liCmp->client_nbr_bonus_jr;
        $client_montant_bonus_jr = $liCmp->client_montant_bonus_jr;
    }
} else {
    $cmp_nbr_bonus = $cmp_montant_bonus = $cmp_nbr_bonus_jr = $cmp_montant_bonus_jr = 0;
    $client_nbr_bonus = $client_montant_bonus = $client_nbr_bonus_jr = $client_montant_bonus_jr = 0;
}
?>
<table align = "center" width=75%>
    <tr><td align="center" colspan='4'><h2   class="htitre">Seuil budgétaire de la campagne</h2></td></tr>
    <tr bgcolor="lightgrey"><td colspan='4'><h3 style="display:inline">Dans la Campagne</h3></td></tr>
    <tr>
        <td>Nombre de bonus</td><td><input type="text" size ="8" name="cmp_nbr_bonus" value ="<?= $cmp_nbr_bonus; ?>" class='chiffre budget'/></td>
        <td>Montant de bonus</td><td><input type="text" size ="12" name="cmp_montant_bonus" value ="<?= $cmp_montant_bonus; ?>" class='chiffre budget'/></td>
    </tr>
    <tr bgcolor="lightgrey"><td colspan='4'><h3 style="display:inline">Par jour</h3></td></tr>
    <tr>
        <td>Nombre de bonus</td><td><input type="text" size ="8" name="cmp_nbr_bonus_jr" value ="<?= $cmp_nbr_bonus_jr; ?>" class='chiffre budget'/></td>
        <td>Montant de bonus</td><td><input type="text" size ="12" name="cmp_montant_bonus_jr" value ="<?= $cmp_montant_bonus_jr; ?>" class='chiffre budget'/></td>
    </tr>
    
    <tr><td align="center" colspan='4'><h2 class="htitre">Seuil budgétaire par abonné</h2></td></tr>
    <tr bgcolor="lightgrey"><td colspan='4'><h3 style="display:inline">Dans la Campagne</h3></td></tr>
    <tr>
        <td>Nombre de bonus</td><td><input type="text" size ="8" name="client_nbr_bonus" value ="<?= $client_nbr_bonus; ?>" class='chiffre budget'/></td>
        <td>Montant de bonus</td><td><input type="text" size ="12" name="client_montant_bonus" value ="<?= $client_montant_bonus; ?>" class='chiffre budget'/></td>
    </tr>
    <tr bgcolor="lightgrey"><td colspan='4'><h3 style="display:inline">Par jour</h3></td></tr>
    <tr>
        <td>Nombre de bonus</td><td><input type="text" size ="8" name="client_nbr_bonus_jr" value ="<?= $client_nbr_bonus_jr; ?>" class='chiffre budget'/></td>
        <td>Montant de bonus</td><td><input type="text" size ="12" name="client_montant_bonus_jr" value ="<?= $client_montant_bonus_jr; ?>" class='chiffre budget'/></td>
    </tr>
</table>