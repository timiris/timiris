<?php
if(!isset($_SESSION)) session_start();
require_once "../../fn_security.php";
check_session(); 
?>
<div id = "cntBonus">
    <?php
    require_once '../bonus/bonus_groupe.php';
    ?>
</div>