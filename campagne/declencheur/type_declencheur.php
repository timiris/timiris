<?php
if (!isset($_SESSION))
    session_start();
require_once "../../fn_security.php";
check_session();
if (isset($_POST['motif']) && !empty($_POST['motif'])) {
    if (addslashes($_POST['motif']) == "evenement") {
        ?>
        <option value ='1'>Appel Emis</option>
        <option value ='2'>Appel Reçu</option>
        <option value ='3'>SMS Emis</option>
        <option value ='4'>SMS Reçu</option>
        <option value ='5'>Service</option>
        <option value ='6'>Recharge</option>
        <option value ='7'>Transfert</option>
        <option value ='8'>Data</option>
        <?php
    }
}
?>