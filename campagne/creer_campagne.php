<?php
if(!isset($_SESSION)) session_start();
require_once "../fn_security.php";
check_session(); 
?>
<h2 align='center'>CRÉATION D'UNE CAMPAGNE</h2>
<hr>
<div id ="informationCampagne">
    <?php
    require "infosCampagne.php";
    ?>
</div>