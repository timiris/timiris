<?php
if(!isset($_SESSION)) session_start();
require_once "../../fn_security.php";
check_session();
echo '<span class="menu_admin" name ="admin/user/creer" style = "position:absolute; right:10px;">Création utilisateur</span>';
echo "<center><div style ='min-width: 900px;' align=center>";
echo "<h2 align = 'center'>Liste des utilisateurs</h2>";
echo "<table class ='dataTable display'>";
echo "<thead><tr><th>Nom</th><th>Prénom</th><th>Login</th><th>Profil</th><th>E-Mail</th><th>Téléphone</th><th>Etat</th><th>Actions</th></tr></thead>";
try {
    require_once '../../conn/connection.php';
    $req = "SELECT su.*, sp.nom sp_nom FROM sys_users su JOIN sys_profil sp on sp.id = su.fk_id_profil order by sp_nom, su.login";
    $result = $connection->query($req);
    $i = 0;
    echo '<tbody>';
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $cls = ($i%2)?'odd':'even';
        if($ligne->etat == 1){
            $etat = "Actif";
            $imgName = 'off';
            $title = 'Désactiver';
        }else{
            $etat = "Incatif";
            $imgName = 'on';
            $title = 'Activer';
        }
        $idUser = $ligne->id;
        echo "<tr class=$cls><td>" . $ligne->nom . "</td>
            <td>" . $ligne->prenom . "</td>
            <td>" . $ligne->login . "</td>
            <td>" . $ligne->sp_nom . "</td>
            <td>" . $ligne->mail . "</td>
            <td>" . $ligne->phone . "</td>
            <td>" . $etat . "</td>
            <td style ='white-space: nowrap;text-align:right;'>
                <img src = 'img/$imgName.png' class = 'actionUser' name = 'gestion$idUser' title = '$title'> 
                <img src = 'img/edit.png' class = 'actionUser edit' name = 'editer$idUser' title = 'Editer'>
                <img src = 'img/reset.png' class = 'actionUser reset' name = 'reset$idUser' title = 'Reset PWD'>
            </td>
         </tr>";
        $i++;
    }
    echo '</tbody>';
} catch (PDOException $e) {
    echo $e->getMessage();
}
echo "</table></div></center>";
?>
