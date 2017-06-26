<?php
require_once "../fn_security.php";
check_session();
?>
<div align='center'>
    <h2>Informations du client</h2>
    <form id ="formInfosNumero">
        <label for = "idNumeroInfos" style = "font-size:16px; color: #0099cc; font-weight:bold; font-size:150%;" autofocus>Numéro de la ligne : </label>
        <input type = "text" size = "11" maxlength = "11" id = "idNumeroInfos" class = "chiffre1" name = "numero_infos" style = "font-size:150%; margin-left:15px; margin-right:15px; text-align: center;">
        <input type = "submit" name = "afficher_numero_infos" id = "idAfficherInfosNumero" value = "Afficher Infos" class="button12 blue">
    </form>
</div>
<hr>
<div id ="informationClient"></div>