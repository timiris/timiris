<?php
if(!isset($_SESSION)) session_start();
require_once "../../fn_security.php";
check_session();
$nom = $prenom = $login = $profil = $phone = $mail = '';
$iduser = null;
$idButton = 'enregistrer_nouvel_user';
require_once '../../conn/connection.php';
echo '<span class="menu_admin" name ="admin/user/list" style = "position:absolute; right:10px;">Liste utilisateurs</span>';
echo "<center><div style ='max-width: 800px;' align=center>";
echo '<div id = "spanRetour" class="alert-box"></div>';
if (isset($_POST['user'])) {
    $req_user = 'select * from sys_users  where id = ' . $_POST['user'];
    $res_user = $connection->query($req_user);
    if ($res_user->rowCount()) {
        $user = $res_user->fetch(PDO::FETCH_OBJ);
        $iduser = $user->id;
        $nom = $user->nom;
        $prenom = $user->prenom;
        $login = $user->login;
        $phone = $user->phone;
        $mail = $user->mail;
        $profil = $user->fk_id_profil;
        echo "<h2 align = 'center'>Modification utilisateur</h2>";
        echo "<input type = 'hidden' width = '0' id = 'iduser' class = 'nv_user' value = '$iduser'/>";
    }
} else {
    echo "<h2 align = 'center'>Création d'un utilisateur</h2>";
}
echo "<table width = '60%' cellspacing = 0 align = 'center' class= 'dataTable display'>";
$options = "";
try {
    $req = "SELECT * from sys_profil where etat = 1";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        if ($ligne->id == $profil)
            $options .= "<option value = " . $ligne->id . " selected>" . $ligne->nom . "</option>";
        else
            $options .= "<option value = " . $ligne->id . ">" . $ligne->nom . "</option>";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
echo "<tbody>
      <tr role = 'row' class = 'odd'><td>Nom</th>
        <td><input type = 'text' width = '80' id = 'nom' required='' class = 'nv_user' value = '$nom'/></td>
      </tr>
      <tr role = 'row' class = 'even'><td>Prénom</td>
        <td><input type = 'text' width = '80' id = 'prenom' required='' class = 'nv_user' value = '$prenom'/></td>
      </tr>
      <tr role = 'row' class = 'odd'><td>E-Mail</td>
        <td><input type = 'text' width = '80' id = 'mail' required='' class = 'nv_user' value = '$mail'/></td>
      </tr>
      <tr role = 'row' class = 'even'><td>Téléphone</td>
        <td><input type = 'text' width = '80' id = 'phone' required='' class = 'nv_user' value = '$phone'/></td>
      </tr>";
if($iduser === null)
      echo "<tr role = 'row' class = 'odd'><td>Login</td>
        <td><input type = 'text' width = '80' id = 'login' required='' class = 'nv_user' value = '$login'/></td>
      </tr>";
echo "<tr role = 'row' class = 'even'><td>Profil</td>
        <td><select width = '80' id = 'profil' class = 'nv_user'>$options</select></td>
      </tr></tbody>";
echo "</table>";
echo "<p align = 'center'><br/><input type = 'button' class ='button12 blue' value = 'Enregister' id = '$idButton'/></p>";

echo "</div></center>";
?>