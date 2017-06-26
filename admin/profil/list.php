<?php
if(!isset($_SESSION)) session_start();
require_once "../../fn_security.php";
check_session();
echo '<span class="menu_admin" name ="admin/profil/creer" style = "position:absolute; right:10px;">Création profil</span>';
echo "<h2 align = 'center'>Liste des profils des utilisateurs</h2>";
echo '<div style = "margin:auto; width : 50%; padding: 10px;">';
echo "<table class ='dataTable display'>";
echo "<thead><tr><th>Nom</th><th>Etat</th><th width='50px'>Actions</th></tr></thead><tbody>";
$req = "SELECT * FROM sys_profil order by nom";
try {
    require_once '../../conn/connection.php';
    $result = $connection->query($req);
    $i = 0;
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $cls = ($i%2)?'odd':'even';
        $i++;
//        $etat = ($ligne->etat == 1) ? "Actif" : "Incatif";
        if($ligne->etat == 1){
            $etat = "Actif";
            $imgName = 'off';
            $title = 'Désactiver';
        }else{
            $etat = "Incatif";
            $imgName = 'on';
            $title = 'Activer';
        }
        $idProfil = $ligne->id;
        echo "<tr class=$cls><td>" . $ligne->nom . "</td><td>" . $etat . "</td>
        <td style ='white-space: nowrap;text-align:right;'>
            <img src = 'img/$imgName.png' class = 'actionProfil' name = 'gestion$idProfil' title = '$title'> 
            <img src = 'img/wf.png' class = 'actionProfil wf' name = 'wf$idProfil' title = 'WorkFlow'> 
            <img src = 'img/permission1.png' class = 'actionProfil edit' name = 'editer$idProfil' title = 'droits'>
        </td></tr>";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
echo "</tbody></table></div>";
?>
