<?php
if(!isset($_SESSION)) session_start();
require_once "../../fn_security.php";
check_session();
require_once '../../conn/connection.php';
if(isset($_POST['profil'])){
    $cls = ' and sap.id_profil = '.(int)$_POST['profil'];
    $req_menu = "select sa.*, sap.id_profil from sys_actions sa left join sys_actions_profil sap on sa.id = sap.id_action $cls 
                    where categorie = 'menu' order by id_paire, id ";
    $title = 'Modification d\'un profil';
    $req = 'select * from sys_profil where id = '.(int)$_POST['profil'];
    $result = $connection->query($req);
    $profil = $result->fetch(PDO::FETCH_OBJ);
    $nom = $profil->nom;
    $id = (int)$_POST['profil'];
}else{
    $req_menu = "select sa.*, NULL id_profil from sys_actions sa where categorie = 'menu' order by id_paire, id ";
    $title = 'Création d\'un profil';
    $nom = '';
    $id = 'NewProfil';
}
$result = $connection->query($req_menu);
$active = ' active ';

echo '<span class="menu_admin" name ="admin/profil/list" style = "position:absolute; right:10px;">Liste profils</span>';
echo '<div style = "margin:auto; width : 50%; padding: 10px;">';
echo "<center><div style ='max-width: 800px;' align=center><div id = 'spanRetour' class='alert-box'></div>";
echo "<table class ='dataTable display'>";
echo "<thead><td colspan ='3'><h2 align = 'center'>$title</h2></td></tr></thead>";
echo "<tbody><th class = 'even' align='right'>Nom du profil </th><td width=15></td>
        <td><input type = 'text' width = '100' id = 'name_profil' value = '$nom' /><input type = 'hidden' id = 'id_profil' value = '$id' /></td>
        </tr>";
echo "<tr><td colspan='3'><center><h2>Liste des permissions</h2></center></td></tr>";
$cls = 'odd';
$i = 1;

while ($menu = $result->fetch(PDO::FETCH_OBJ)) {
    if ($menu->id_paire == 0)
        $arr_menu[$menu->id] = array('libelle' => $menu->libelle, 'id_profil' => $menu->id_profil, 'fils' => array());
    else if(isset($arr_menu[$menu->id_paire]['fils']))
        $arr_menu[$menu->id_paire]['fils'][$menu->id] = array('libelle' => $menu->libelle, 'id_profil' => $menu->id_profil, 'fils' => array());

}
foreach ($arr_menu as $id => $inf_menu) {
    $img = isset($inf_menu['id_profil']) ? 'on1' : 'off1';
    echo '<tr class="'.$cls.'">
            <td align=right><img src="img/'.$img.'.png" id = '.$id.' class ="nv_profil img_action_profil actionProfil"></td>
            <td colspan=2>' . $inf_menu['libelle'] . '</td>
        </tr>';
    $i++;
    $cls = ($i%2)?'odd':'even';
    if (count($inf_menu['fils'])) {
        foreach ($inf_menu['fils'] as $idEnf => $infEnf) {
            $img = ($infEnf['id_profil'] !== NULL) ? 'on1' : 'off1';
            echo '<tr class="'.$cls.'">
                <td align=right colspan="2"><img src="img/'.$img.'.png" id = '.$idEnf.' class ="nv_profil img_action_profil actionProfil"></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $infEnf['libelle'].'</td>
            </tr>';
        }
        $i++;
        $cls = ($i%2)?'odd':'even';
    }
}
echo "<tr><td colspan = '3' align = 'center'><br/><br/><input type = 'button' class ='button12 blue' id ='idEnregistrerProfil' value = 'Enregister'/></td></tr></tbody>";
echo "</table></div>";
?>
