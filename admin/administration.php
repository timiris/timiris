<?php

require_once "../fn_security.php";
check_session();
?>
<div id ="divMenuAdmin" class="divShadow">
    <span class="menu_admin" name ="profil/list">Gestion des profils</span>
    <span class="menu_admin" name ="user/list">Gestion des utilisateurs</span>
</div>
<div id ="divContentAdmin"></div>