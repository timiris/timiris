<?php

require_once "conn/connection.php";
require_once "defs.php";
$reqCEV = 'SELECT count(*) nbr FROM app_campagne where etat = ' . CMP_SOUMISE . ' and chez_profil = ' . $_SESSION["user"]["profil"];
$resCEV = $connection->query($reqCEV)->fetch(PDO::FETCH_OBJ);
if ($resCEV->nbr){
    
    echo '<h2 align="center" class="alert-box warning">Bonjour ' . $_SESSION["user"]["prenom"] . ' ' . $_SESSION["user"]["nom"] . ', 
        vous avez '.$resCEV->nbr.' campagne pour validation <a href="menu_6|menu_20" class="menu_racc">Cliquez ici</a></h2>';
}
else
    echo '<h2 align="center" class="alert-box notice">' . $_SESSION["user"]["prenom"] . ' ' . $_SESSION["user"]["nom"] . ', bienvenue dans la plateforme TIMIRIS</h2>';
?>