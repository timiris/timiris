<?php
if(!isset($_SESSION)) session_start();
require_once "../../fn_security.php";
check_session(); 
$pwd = $nv_pwd = $conf_pwd = '';
try {
    if (isset($_POST['user']) && !empty($_POST['user'])) {
        $userTb = json_decode($_POST["user"], true);
        $user = $userTb['iduser'];
        $pwd = $userTb['act_pwd'];
        $nv_pwd = $userTb['nv_pwd'];
        $conf_pwd = $userTb['conf_pwd'];
        $retour = array();
        $retour['exec'] = 0;
        $retour['message'] = '';
        if ($nv_pwd == $conf_pwd && $conf_pwd != '') {
            require_once '../../conn/connection.php';
            $req = "select * from sys_users where id = $user and pwd = '" . md5($pwd) . "'";
            $res = $connection->query($req);
            if ($res->rowCount()) {
                $req_ins = "update sys_users set pwd = '" . md5($nv_pwd) . "' where id = $user";
                $res = $connection->query($req_ins);
                $retour['message'] = 'Mot de passe modifié avec succès';
                $retour['exec'] = 1;
            }else
                $retour['message'] = 'Mot de passe invalide';
        }else
            $retour['message'] = 'Données de formulaire incorrectes';
        echo json_encode($retour);
        exit();
    }
    
    echo "<center><div style ='max-width: 700px;' align=center><br><div id = 'spanRetour'></div>";
    echo "<table width = '100%' cellspacing = 0 align = 'center' class= 'dataTable display'>";
    echo "<input type = 'hidden' width = '0' id = 'iduser' class = 'mod_pwd' value = '" . $_SESSION['user']['id'] . "'/>";
    echo "<tbody>
            <tr role = 'row' class = 'odd'><th colspan=2><h2>Modification mot de passe</h2> </th></tr>
            <tr role = 'row' class = 'even'><td>Mot de passe actuel</td>
                <td><input type = 'password' width = '80' id = 'act_pwd' class = 'mod_pwd' value = '$pwd'/></td>
            </tr>
            <tr role = 'row' class = 'even'><td>Nouveau Mot de passe</td>
                <td><input type = 'password' width = '80' id = 'nv_pwd' class = 'mod_pwd' value = '$nv_pwd'/></td>
            </tr>
            <tr role = 'row' class = 'even'><td>Confirmation Mot de passe</td>
                <td><input type = 'password' width = '80' id = 'conf_pwd' class = 'mod_pwd' value = '$conf_pwd'/><br><br></td>
            </tr>
            <tr role = 'row' class = 'odd'>
                <td colspan=2 align=center><input type = 'button' class = 'button12 black' value = 'Enregistrer' id='idEnrModPWD'></td>
            </tr>
            </tbody>";
    echo "</table></div></center>";
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>