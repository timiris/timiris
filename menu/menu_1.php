<?php
require_once "fn_security.php";
check_session();
?>
<div id="cssmenu">
    <ul>
        <?php
        $arr_menu = array();
        require_once 'conn/connection.php';
        $req_menu = "select * from sys_actions where id in (" . implode(', ', $_SESSION["action"]) . ") order by id_paire, id";
        $result = $connection->query($req_menu);
        $urlDefault = '';
        while ($menu = $result->fetch(PDO::FETCH_OBJ)) {
            if ($menu->url == 'bienvenu') {
                $urlDefault = $menu->url;
                $active = ' active ';
            }
            else
                $active = '';
            if ($menu->id_paire == 0)
                $arr_menu[$menu->id] = array('libelle' => $menu->libelle, 'url' => $menu->url, 'active' => $active, 'fils' => array());
            else
                $arr_menu[$menu->id_paire]['fils'][$menu->id] = array('libelle' => $menu->libelle, 'url' => $menu->url, 'active' => $active, 'fils' => array());
            
        }
        if ($urlDefault == '')
            $urlDefault = 'accueil';
        foreach ($arr_menu as $id => $inf_menu) {
            if (count($inf_menu['fils'])) {
                echo '<li><a href="#" class="parent has-sub menuVertical' . $inf_menu['active'] . '">' .$inf_menu['libelle'] . '</a>';
                echo '<ul class="sous-menu">';
                foreach ($inf_menu['fils'] as $idEnf => $infEnf) {
                    echo '<li><a href="' . $infEnf['url'] . '" class="menuVertical lism' . $infEnf['active'] . '"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $infEnf['libelle'] . '</a></li>';
                }
                echo '</ul>
                    </li>';
            }else{
                 echo '<li><a href="' . $inf_menu['url'] . '" class="parent menuVertical ' . $inf_menu['active'] . '">'. $inf_menu['libelle'] . '</a></li>';
            }
        }
        ?>
    </ul>
</div>