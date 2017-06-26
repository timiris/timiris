<?php
if(!isset($rep))
    $rep = '../';
require_once $rep."fn_security.php";
check_session(); 
?>
<center><h1>CIBLAGE DES ABONNÉS</h1></center>
<label for="idCibleName">Nom de la cible : </label><input type="text" class="tgCategory" id="idCibleName" size = 30>