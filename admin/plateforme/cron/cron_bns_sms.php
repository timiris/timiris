<?php

echo "<center><h2 class='htitre'>Attribution BONUS & Envoi SMS</h2></center>";
echo '<div style = "margin:auto; padding: 10px;">';
echo "<table class ='dataTable display'>";
echo "<thead><tr><th>Programme</th><th>Etat</th><th width='50px'>Actions</th></tr></thead><tbody>";
try {
    require_once $rep . 'conn/connection.php';
    $result = $connection->query("SELECT * FROM sys_cron where nature != 'cdr' order by libelle");
    $i = 0;
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $cls = ($i % 2) ? 'odd' : 'even';
        $i++;
//        $etat = ($ligne->etat == 1) ? "Actif" : "Incatif";
        if ($ligne->etat) {
            $etat = "Actif";
            $imgName = 'off';
            $title = 'Désactiver';
        } else {
            $etat = "Incatif";
            $imgName = 'on';
            $title = 'Activer';
        }
        $type = $ligne->type;
        echo "<tr class=$cls><td>" . $ligne->libelle . "</td><td>" . $etat . "</td>
        <td style ='white-space: nowrap;text-align:right;'>
            <img src = 'img/$imgName.png' class = 'actionCron' name = '$type' title = '$title'> 
        </td></tr>";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
echo "</tbody></table></div>";
?>